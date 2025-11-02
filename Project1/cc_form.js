const caterers = [
    { first: "John", last: "Smith", password: "*Pass1", id: "1234", phone: "973-555-1111 ext.12", email: "john@cc.com" },
    { first: "Lisa", last: "Patel", password: "!Cake2", id: "5678", phone: "908-333-2222 ext.09", email: "lisa@cc.com" },
    { first: "Mike", last: "Rodriguez", password: "#Chef3", id: "2345", phone: "201-777-9999 ext.34", email: "mike@cc.com" },
    { first: "Sarah", last: "Nguyen", password: "$Bake4", id: "3456", phone: "732-888-1234 ext.56", email: "sarah@cc.com" },
    { first: "Kevin", last: "Brown", password: "%Dish5", id: "4567", phone: "848-111-2222 ext.33", email: "kevin@cc.com" },
    { first: "Aisha", last: "Ali", password: "&Cook6", id: "6789", phone: "609-555-9876 ext.77", email: "aisha@cc.com" },
    { first: "Ravi", last: "Desai", password: "*Food7", id: "7890", phone: "732-444-7890 ext.90", email: "ravi@cc.com" },
    { first: "Maria", last: "Lopez", password: "!Meal8", id: "8901", phone: "908-222-3333 ext.45", email: "maria@cc.com" },
    { first: "James", last: "Kim", password: "@Chef9", id: "9012", phone: "973-999-0000 ext.01", email: "james@cc.com" },
    { first: "Priya", last: "Singh", password: "#Bake0", id: "1357", phone: "201-123-4567 ext.11", email: "priya@cc.com" }
];

document.getElementById("togglePassword").addEventListener("click", () => {
    const pwd = document.getElementById("password");
    const icon = document.getElementById("togglePassword");
    if (pwd.type === "password") {
        pwd.type = "text";
        icon.textContent = "🙈";
    } else {
        pwd.type = "password";
        icon.textContent = "👁️";
    }
});

function validate() {
    const first = document.getElementById("firstName").value.trim();
    const last = document.getElementById("lastName").value.trim();
    const phone = document.getElementById("phone").value.trim();
    const id = document.getElementById("repId").value.trim();
    const email = document.getElementById("email").value.trim();
    const pwd = document.getElementById("password").value.trim();
    const confirmEmail = document.getElementById("emailConfirm").checked;
    const transaction = document.getElementById("transaction").value;

    const namePattern = /^[A-Za-z'-]{2,}$/;
    const phonePattern = /^\d{3}[- ]?\d{3}[- ]?\d{4}\s*ext\.\d{2,3}$/;
    const idPattern = /^\d{4}$/;
    const emailPattern = /^[^\s@]+@[^\s@]+\.[A-Za-z]{1,3}$/;
    const passwordPattern = /^[^A-Za-z0-9](?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{4,5}$/;

    if (!namePattern.test(first)) {
        alert("Invalid first name. Use only letters (min 2 characters).");
        document.getElementById("firstName").focus();
        return false;
    }
    if (!namePattern.test(last)) {
        alert("Invalid last name. Use only letters (min 2 characters).");
        document.getElementById("lastName").focus();
        return false;
    }
    if (!phonePattern.test(phone)) {
        alert("Phone format invalid. Use ###-###-#### ext.##");
        document.getElementById("phone").focus();
        return false;
    }
    if (!idPattern.test(id)) {
        alert("Caterer ID must be 4 digits.");
        document.getElementById("repId").focus();
        return false;
    }
    if (!passwordPattern.test(pwd)) {
        alert("Password must start with a special character, have 1 uppercase, 1 number, and max 5 chars.");
        document.getElementById("password").focus();
        return false;
    }
    if (confirmEmail && !emailPattern.test(email)) {
        alert("Email is required and must be in valid format (example@domain.com).");
        document.getElementById("email").focus();
        return false;
    }
    if (transaction === "") {
        alert("Please select a transaction type.");
        document.getElementById("transaction").focus();
        return false;
    }

    return verify(first, last, pwd, id, phone, email, confirmEmail, transaction);
}

function verify(first, last, pwd, id, phone, email, confirmEmail, transaction) {
    const match = caterers.find(c =>
        c.first.toLowerCase() === first.toLowerCase() &&
        c.last.toLowerCase() === last.toLowerCase() &&
        c.password === pwd &&
        c.id === id &&
        c.phone === phone &&
        (!confirmEmail || c.email === email)
    );

    if (match) {
        alert(`Welcome ${match.first} ${match.last}! You have successfully entered the system to perform: ${transaction}.`);
    } else {
        alert(`Caterer ${first} ${last} cannot be found. Please check your login details.`);
    }

    return false;
}
