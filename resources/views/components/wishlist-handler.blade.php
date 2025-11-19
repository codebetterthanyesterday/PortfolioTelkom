<script>
/**
 * Setup wishlist form handlers with AJAX
 * Handles toggle functionality for wishlist buttons across the application
 */
function setupWishlistForms() {
    document.querySelectorAll('.wishlist-form').forEach(form => {
        // Remove existing listeners to prevent duplicates
        form.replaceWith(form.cloneNode(true));
    });
    
    // Re-query after cloning to get fresh elements
    document.querySelectorAll('.wishlist-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(form);
            const button = form.querySelector('button');
            const icon = button.querySelector('i');
            const originalIconClasses = icon.className;
            
            // Disable button during request
            button.disabled = true;
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Toggle icon classes based on wishlist status
                    if (data.isWishlisted) {
                        // Wishlisted state - filled heart with red color
                        icon.className = 'ri-heart-fill text-[#b01116]';
                    } else {
                        // Not wishlisted - outline heart with gray color
                        icon.className = 'ri-heart-line text-gray-600';
                    }
                    
                    // Show success toast
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    }).fire({
                        icon: 'success',
                        title: data.message
                    });
                } else {
                    // Revert icon on error
                    icon.className = originalIconClasses;
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#b01116'
                    });
                }
            } catch (error) {
                // Revert icon on error
                icon.className = originalIconClasses;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan jaringan',
                    confirmButtonColor: '#b01116'
                });
            } finally {
                // Re-enable button
                button.disabled = false;
            }
        });
    });
}

/**
 * Setup wishlist remove handlers for profile page
 * Handles removing items from wishlist with confirmation
 */
function setupWishlistRemoveForms() {
    document.querySelectorAll('.wishlist-remove-form').forEach(form => {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            // Show confirmation dialog
            const result = await Swal.fire({
                title: 'Hapus dari Wishlist?',
                text: 'Apakah Anda yakin ingin menghapus proyek ini dari wishlist?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#b01116',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            });
            
            if (!result.isConfirmed) {
                return;
            }
            
            const formData = new FormData(form);
            const projectCard = form.closest('.wishlist-project-card');
            
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Fade out and remove the card
                    if (projectCard) {
                        projectCard.style.transition = 'opacity 0.3s ease-out';
                        projectCard.style.opacity = '0';
                        
                        setTimeout(() => {
                            projectCard.remove();
                            
                            // Check if wishlist is now empty
                            const remainingCards = document.querySelectorAll('.wishlist-project-card');
                            if (remainingCards.length === 0) {
                                const wishlistContainer = document.querySelector('#wishlist-projects');
                                if (wishlistContainer) {
                                    wishlistContainer.innerHTML = `
                                        <div class="col-span-full text-center py-12">
                                            <i class="ri-heart-line text-6xl text-gray-400 mb-4"></i>
                                            <p class="text-gray-500">Belum ada proyek di wishlist</p>
                                        </div>
                                    `;
                                }
                            }
                        }, 300);
                    }
                    
                    // Show success toast
                    Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true
                    }).fire({
                        icon: 'success',
                        title: data.message
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.message || 'Terjadi kesalahan',
                        confirmButtonColor: '#b01116'
                    });
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan jaringan',
                    confirmButtonColor: '#b01116'
                });
            }
        });
    });
}
</script>

<script>
// Auto-initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    setupWishlistForms();
    setupWishlistRemoveForms();
});
</script>
