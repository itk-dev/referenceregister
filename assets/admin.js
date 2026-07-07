// https://github.com/Energy-Sparks/energy-sparks/pull/2112/changes
addEventListener("trix-initialize", (event) => {
    const { toolbarElement } = event.target;
    const inputElement = toolbarElement?.querySelector("input[name=href]");
    if (inputElement) {
        inputElement.type = "text";
        inputElement.pattern = "(https?://|/).+";
    }
});
