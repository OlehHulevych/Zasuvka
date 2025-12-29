export const validateString = (value) => {
    const htmlPattern = /[&<>"']/;
    return htmlPattern.test(value);
}