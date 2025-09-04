document.addEventListener('alpine:init', () => {
    Alpine.data('imageModal', () => ({
        selectedImage: null,
        showModal: false,

        showImage(image) {
            this.selectedImage = image;
            this.showModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeModal() {
            this.showModal = false;
            this.selectedImage = null;
            document.body.style.overflow = 'auto';
        },

        init() {
            // Close modal on Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.showModal) {
                    this.closeModal();
                }
            });
        }
    }));

    // Language switcher
    Alpine.data('languageSwitcher', () => ({
        currentLang: document.documentElement.lang || 'en',

        switchLanguage(lang) {
            this.currentLang = lang;
            // You would implement the actual language switching logic here
            // For example, making an AJAX request to change the locale
            fetch('/language/' + lang, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ language: lang })
            }).then(() => {
                window.location.reload();
            });
        }
    }));
});

// Image lazy loading
document.addEventListener('DOMContentLoaded', function() {
    const images = document.querySelectorAll('img[data-src]');

    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => imageObserver.observe(img));
});
