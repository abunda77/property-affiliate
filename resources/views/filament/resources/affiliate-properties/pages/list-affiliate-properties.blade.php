<x-filament-panels::page>
    <div x-data="{
        copyToClipboard(text) {
            console.log('Attempting to copy:', text);
    
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(text)
                    .then(() => {
                        console.log('Copy successful!');
                        $wire.dispatch('notify', {
                            message: 'Link berhasil disalin!',
                            type: 'success'
                        });
                    })
                    .catch((err) => {
                        console.error('Copy failed:', err);
                        alert('Link berhasil disalin!');
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
                    console.log('Fallback copy successful');
                    alert('Link berhasil disalin!');
                } catch (err) {
                    console.error('Fallback copy failed:', err);
                    alert('Gagal menyalin link');
                }
                document.body.removeChild(textArea);
            }
        }
    }" x-on:copy-to-clipboard.window="copyToClipboard($event.detail.text)">
        {{ $this->table }}
    </div>
</x-filament-panels::page>
