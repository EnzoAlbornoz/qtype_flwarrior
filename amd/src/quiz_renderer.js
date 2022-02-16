// // Define Helpers
// const XML_CHAR_MAP = {
//     '<': '&lt;',
//     '>': '&gt;',
//     '&': '&amp;',
//     '"': '&quot;',
//     "'": '&apos;'
// };

// /**
//  *
//  * @param {string} s
//  * @returns
//  */
// function escapeXml (s) {
//     return s.replace(/[<>&"']/g, function (ch) {
//         return XML_CHAR_MAP[ch];
//     });
// }

// const HTML_CHAR_MAP = {
//     '<': '&lt;',
//     '>': '&gt;',
//     '&': '&amp;',
//     '"': '&quot;',
//     "'": '&#39;'
// };

// function escapeHtml (s) {
//     return s.replace(/[<>&"']/g, function (ch) {
//         return HTML_CHAR_MAP[ch];
//     });
// }
// Export Functions
/**
 *
 * @param {string} sla
 */
export const init = (sla) => {
    window.console.log("[flwarrior] Loading module", sla);

    /** @type {HTMLInputElement} */
    const inputData = document.querySelector('[name="machine"]');
    /** @type {HTMLInputElement} */
    const hiddenData = document.querySelector('#machine_serialized');
    /** @type {HTMLParagraphElement} */
    // const machineLog = document.querySelector('#machine_log');

    window.console.log(typeof inputData.value, inputData.value);

    inputData.addEventListener("input", async () => {
        // Fetch Data from Input
        const file = inputData.files.item(0);
        const content = await file.text();
        window.console.log(content);
        hiddenData.value = content;
        // machineLog.textContent = "Loaded!";
    });

    window.console.log("[flwarrior] Module loaded");
};
