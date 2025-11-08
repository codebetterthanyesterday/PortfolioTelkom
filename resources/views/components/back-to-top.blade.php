<button id="back-to-top" class="fixed bottom-6 cursor-pointer right-6 bg-[#b01116] text-white w-12 h-12 rounded-full shadow-lg hover:bg-[#8d0d11] transition-all duration-300 ease-in-out opacity-0 invisible flex items-center justify-center z-50 hover:scale-110">
    <i class="ri-arrow-up-line text-xl"></i>
</button>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const backToTopButton = document.getElementById('back-to-top');
        
        if (backToTopButton) {
            // Show/hide button based on scroll position
            window.addEventListener('scroll', function() {
                if (window.scrollY > 300) {
                    backToTopButton.classList.remove('opacity-0', 'invisible');
                    backToTopButton.classList.add('opacity-100', 'visible');
                } else {
                    backToTopButton.classList.add('opacity-0', 'invisible');
                    backToTopButton.classList.remove('opacity-100', 'visible');
                }
            });

            // Smooth scroll to top
            backToTopButton.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
    });
</script>
