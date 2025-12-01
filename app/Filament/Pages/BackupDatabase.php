<?php

namespace App\Filament\Pages;

use App\Models\BackupLog;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class BackupDatabase extends Page implements HasTable
{
    use InteractsWithTable;

    protected static \UnitEnum|string|null $navigationGroup = 'Settings';

    protected static ?string $title = 'Backup Database';

    protected static ?string $slug = 'backup-database';

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.pages.backup-database';

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-server';
    }

    public static function getNavigationLabel(): string
    {
        return 'Backup Database';
    }

    public static function canAccess(): bool
    {
        return auth()->check() && auth()->user()->hasRole('super_admin');
    }

    private const BACKUP_PATH = 'backup';

    private const MYSQLDUMP_PATH = [
        'windows' => 'C:\\laragon\\bin\\mysql\\mysql-8.0.30-winx64\\bin\\mysqldump.exe',
        'linux' => 'mysqldump', // Use system PATH instead of hardcoded path
    ];

    public function table(Table $table): Table
    {
        return $table
            ->query(BackupLog::query())
            ->columns($this->getTableColumns())
            ->actions($this->getTableActions())
            ->bulkActions($this->getTableBulkActions())
            ->headerActions($this->getTableHeaderActions())
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    private function getTableColumns(): array
    {
        return [
            TextColumn::make('filename')
                ->label('Nama File')
                ->searchable()
                ->sortable(),
            TextColumn::make('type')
                ->label('Tipe')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'manual' => 'warning',
                    'scheduled' => 'success',
                    default => 'gray'
                }),
            TextColumn::make('status')
                ->label('Status')
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'success' => 'success',
                    'failed' => 'danger',
                    default => 'warning'
                }),
            TextColumn::make('formatted_size')
                ->label('Ukuran'),
            TextColumn::make('created_at')
                ->label('Tanggal')
                ->dateTime('d M Y H:i:s')
                ->sortable(),
        ];
    }

    private function getTableActions(): array
    {
        return [
            Action::make('download')
                ->label('Download')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('warning')
                ->action(fn (BackupLog $record) => $this->handleDownload($record))
                ->visible(fn (BackupLog $record) => $record->status === BackupLog::STATUS_SUCCESS),

            Action::make('delete')
                ->label('Hapus')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->action(fn (BackupLog $record) => $this->handleDelete($record)),
        ];
    }

    private function handleDownload(BackupLog $record)
    {
        $fullPath = storage_path('app/'.$record->path);

        if (! File::exists($fullPath)) {
            $this->sendErrorNotification('File tidak ditemukan');

            return;
        }

        return response()->download(
            $fullPath,
            $record->filename,
            ['Content-Type' => 'application/sql']
        );
    }

    private function handleDelete(BackupLog $record)
    {
        try {
            $this->deleteBackupFile($record);
            $record->delete();
            $this->sendSuccessNotification('Backup berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting backup: '.$e->getMessage());
            $this->sendErrorNotification('Gagal menghapus backup', 'Terjadi kesalahan saat menghapus file backup');
        }
    }

    private function deleteBackupFile(BackupLog $record): void
    {
        $fullPath = storage_path('app/'.$record->path);

        if (File::exists($fullPath)) {
            File::delete($fullPath);
        }
    }

    private function getTableBulkActions(): array
    {
        return [
            BulkAction::make('delete')
                ->label('Hapus yang dipilih')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->deselectRecordsAfterCompletion()
                ->action(fn (Collection $records) => $this->handleBulkDelete($records)),
        ];
    }

    private function handleBulkDelete(Collection $records)
    {
        try {
            foreach ($records as $record) {
                $this->deleteBackupFile($record);
                $record->delete();
            }
            $this->sendSuccessNotification('Backup berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Error deleting backups: '.$e->getMessage());
            $this->sendErrorNotification('Gagal menghapus backup', 'Terjadi kesalahan saat menghapus file backup');
        }
    }

    private function getTableHeaderActions(): array
    {
        return [
            Action::make('create_backup')
                ->label('Backup Sekarang')
                ->icon('heroicon-o-plus')
                ->color('warning')
                ->action(fn () => $this->createBackup()),
        ];
    }

    private function createBackup()
    {
        try {
            $filename = $this->generateBackupFilename();
            $path = $this->ensureBackupDirectory();
            $command = $this->buildMysqlDumpCommand($filename);

            // Log the command for debugging (without password)
            $logCommand = preg_replace('/--password=[^\s]+/', '--password=***', $command);
            Log::info('Executing backup command: '.$logCommand);

            $output = [];
            $resultCode = null;
            exec($command, $output, $resultCode);

            // Log output for debugging
            if (! empty($output)) {
                Log::info('Backup command output: '.implode("\n", $output));
            }

            $this->validateAndLogBackup($filename, $path, $resultCode, $output);
        } catch (\Exception $e) {
            Log::error('Backup error: '.$e->getMessage());
            $this->sendErrorNotification('Backup gagal', $e->getMessage());
        }
    }

    private function generateBackupFilename(): string
    {
        return 'backup-'.Carbon::now()->format('Y-m-d-H-i-s').'.sql';
    }

    private function ensureBackupDirectory(): string
    {
        $path = storage_path('app/'.self::BACKUP_PATH);
        if (! File::exists($path)) {
            File::makeDirectory($path, 0755, true);
        }

        return $path;
    }

    private function buildMysqlDumpCommand(string $filename): string
    {
        $os = PHP_OS_FAMILY === 'Windows' ? 'windows' : 'linux';
        $mysqldumpPath = self::MYSQLDUMP_PATH[$os] ?? 'mysqldump';

        $username = escapeshellarg(config('database.connections.mysql.username'));
        $password = config('database.connections.mysql.password');
        $database = escapeshellarg(config('database.connections.mysql.database'));
        $host = escapeshellarg(config('database.connections.mysql.host'));
        $port = escapeshellarg(config('database.connections.mysql.port'));
        $outputFile = escapeshellarg($this->ensureBackupDirectory().'/'.$filename);

        // For Linux/production, use single quotes and proper escaping
        if ($os === 'linux') {
            if (empty($password)) {
                return sprintf(
                    '%s --user=%s --host=%s --port=%s --single-transaction --quick --lock-tables=false %s > %s 2>&1',
                    $mysqldumpPath,
                    $username,
                    $host,
                    $port,
                    $database,
                    $outputFile
                );
            }

            return sprintf(
                '%s --user=%s --password=%s --host=%s --port=%s --single-transaction --quick --lock-tables=false %s > %s 2>&1',
                $mysqldumpPath,
                $username,
                escapeshellarg($password),
                $host,
                $port,
                $database,
                $outputFile
            );
        }

        // Windows command (original logic)
        if (empty($password)) {
            return sprintf(
                '"%s" --user=%s --host=%s --port=%s --column-statistics=0 %s > %s',
                $mysqldumpPath,
                $username,
                $host,
                $port,
                $database,
                $outputFile
            );
        }

        return sprintf(
            '"%s" --user=%s --password=%s --host=%s --port=%s --column-statistics=0 %s > %s',
            $mysqldumpPath,
            $username,
            escapeshellarg($password),
            $host,
            $port,
            $database,
            $outputFile
        );
    }

    private function validateAndLogBackup(string $filename, string $path, ?int $resultCode, array $output = [])
    {
        $fullPath = $path.'/'.$filename;

        if ($resultCode !== 0) {
            $errorMessage = ! empty($output) ? implode("\n", $output) : 'Unknown error';
            Log::error('Backup failed with code '.$resultCode.': '.$errorMessage);
            throw new \Exception('Backup gagal dengan kode: '.$resultCode.'. Error: '.$errorMessage);
        }

        if (! File::exists($fullPath)) {
            throw new \Exception('Backup file tidak ditemukan setelah eksekusi');
        }

        if (File::size($fullPath) === 0) {
            throw new \Exception('Backup file kosong (0 bytes)');
        }

        BackupLog::create([
            'filename' => $filename,
            'path' => self::BACKUP_PATH.'/'.$filename,
            'size' => File::size($fullPath),
            'type' => BackupLog::TYPE_MANUAL,
            'status' => BackupLog::STATUS_SUCCESS,
            'notes' => 'Backup berhasil dibuat',
        ]);

        $this->sendSuccessNotification('Backup berhasil dibuat');
    }

    private function sendSuccessNotification(string $message): void
    {
        Notification::make()
            ->success()
            ->title($message)
            ->send();
    }

    private function sendErrorNotification(string $title, ?string $body = null): void
    {
        $notification = Notification::make()
            ->danger()
            ->title($title);

        if ($body) {
            $notification->body($body);
        }

        $notification->send();
    }
}
