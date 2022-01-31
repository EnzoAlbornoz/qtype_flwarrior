// Import Dependencies
// Export Functions
export const init = () => {
    const button = document.querySelector( '[name="fl-btn-add-test"]');

    let createdNodes = 1;

    button.addEventListener("click", () => {
        // Add New Option
        window.console.log("Hello, World!");
        // Duplicate First Row
        const testInfoGroup = document.querySelector('[data-groupname="machine-test-1"]');
        const duplicatedTestInfoGroup = /** @type {Element} */ testInfoGroup.cloneNode(true);
        // Edit Duplicated Tree
        duplicatedTestInfoGroup.setAttribute("data-groupname",  `machine-test-${++createdNodes}`);
        // Add Node Back to DOM
        testInfoGroup.parentNode.insertBefore(duplicatedTestInfoGroup, testInfoGroup.nextSibling);
        window.console.log(testInfoGroup);
        window.console.log(duplicatedTestInfoGroup);
    });
};
