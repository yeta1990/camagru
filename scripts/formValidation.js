
export function validLength(field){
    return field.length > 0 && field.length <= 256;
}

export function isValidEmail(email){
    return /^[\w-\.]+@([\w-]+\.)+[\w-]{2,10}$/.test(email);
}

export function isValidPassword(password) {
    const minLength = 8;
    const hasUpperCase = /[A-Z]/.test(password);
    const hasLowerCase = /[a-z]/.test(password);
    const hasNumber = /\d/.test(password);
    return password.length >= minLength && hasUpperCase && hasLowerCase && hasNumber;
}