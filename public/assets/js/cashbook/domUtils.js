/**
 * DOM Utility for Mobile Performance
 * Replaces expensive container.innerHTML = string with DocumentFragment injection.
 */
window.injectHTML = function (container, htmlString) {
    // Clear container
    container.innerHTML = '';

    // Parse using template -> DocumentFragment
    const template = document.createElement('template');
    template.innerHTML = htmlString.trim();

    // Append fragment (single layout tick)
    container.appendChild(template.content);
};
