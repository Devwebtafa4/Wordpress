jQuery(document).ready(function($) {
    let currentIndex = 0;
    const items = $('.carousel-item');
    const totalItems = items.length;

    function showSlide(index) {
        if (index < 0) {
            currentIndex = totalItems - 1;
        } else if (index >= totalItems) {
            currentIndex = 0;
        } else {
            currentIndex = index;
        }

        // Move the carousel to the current slide
        $('.carousel').css('transform', 'translateX(' + (-currentIndex * 100) + '%)');
    }

    // Show the next slide
    $('.next-btn').click(function() {
        showSlide(currentIndex + 1);
    });

    // Show the previous slide
    $('.prev-btn').click(function() {
        showSlide(currentIndex - 1);
    });

    // Initialize the first slide
    showSlide(currentIndex);
});

