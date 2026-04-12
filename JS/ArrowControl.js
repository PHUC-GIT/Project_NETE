// JS that control arrow helper
const upArrow = document.querySelector('.up-arrow');
const downArrow = document.querySelector('.down-arrow');

function updateScrollHints() {
    // Get the scroll measurements
    // scrollTop = distance from top
    // scrollHeight = total content height
    // clientHeight = visible window height
    const { scrollTop, scrollHeight, clientHeight } = document.documentElement;

    if (scrollTop <= 5) {
        upArrow.classList.add('disabled');
        upArrow.classList.remove('active');
    } else {
        upArrow.classList.add('active');
        upArrow.classList.remove('disabled');
    }

    // Check if the bottom of the window has reached the bottom of the content
    const isAtBottom = scrollTop + clientHeight >= scrollHeight - 5;

    if (isAtBottom) {
        downArrow.classList.add('disabled');
        downArrow.classList.remove('active');
    } else {
        downArrow.classList.add('active');
        downArrow.classList.remove('disabled');
    }
}

// A bit of optimized scroll.
let isScrolling;
window.addEventListener('scroll', () => {
    if (!isScrolling) {
        window.requestAnimationFrame(() => {
            updateScrollHints();
            isScrolling = false;
        });
        isScrolling = true;
    }
});

window.addEventListener('resize', updateScrollHints);

const observer = new MutationObserver(() => {
    updateScrollHints();
});

// Watch the body for any changes to the content
observer.observe(document.body, { 
    childList: true, 
    subtree: true 
});

// Function to scroll down
downArrow.addEventListener('click', () => {
    window.scrollBy({
        top: window.innerHeight * 0.8, // Scrolls 80% of the screen height
        behavior: 'smooth'
    });
});

// Function to scroll up
upArrow.addEventListener('click', () => {
    window.scrollBy({
        top: -(window.innerHeight * 0.8), // Scrolls up 80%
        behavior: 'smooth'
    });
});