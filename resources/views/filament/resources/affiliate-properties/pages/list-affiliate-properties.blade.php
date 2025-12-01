<x-filament-panels::page>
    <div x-data="{
        copyToClipboard(text) {
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text)
                    .then(() => {
                        new FilamentNotification()
                            .title('Link berhasil disalin!')
                            .success()
                            .send();
                    })
                    .catch(() => {
                        new FilamentNotification()
                            .title('Gagal menyalin link')
                            .body('Silakan copy link secara manual')
                            .danger()
                            .send();
                    });
            } else {
                // Fallback for older browsers
                const textArea = document.createElement('textarea');
                textArea.value = text;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    new FilamentNotification()
                        .title('Link berhasil disalin!')
                        .success()
                        .send();
                } catch (err) {
                    new FilamentNotification()
                        .title('Gagal menyalin link')
                        .body('Silakan copy link secara manual')
                        .danger()
                        .send();
                }
                document.body.removeChild(textArea);
            }
        }
    }" x-on:copy-to-clipboard.window="copyToClipboard($event.detail.text)">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
