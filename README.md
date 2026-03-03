# SE 331: Software Engineering Design Capstone Project  
## Lost & Found Portal

A secure lost & found portal system built with **Pure PHP + JS + MySQL + Tailwind**.

---

## 📦 Installation (XAMPP)
1. Install and configure **XAMPP**.
2. Place the project files in the `htdocs` directory.
3. Import the provided SQL file into **phpMyAdmin** to set up the database.
4. Start **Apache** and **MySQL** services from the XAMPP control panel.
5. Access the portal via `http://localhost/<project-folder>`.

---

## 👨‍💻 Default Admin Setup
- Email: `admin@nexus.com`  
- Password: *(reset required)*  

### Resetting Admin Password
Run the following command in your terminal to generate a new password hash:
```bash
php -r "echo password_hash('NewAdmin123!', PASSWORD_DEFAULT);"
Update the database with the new password and ensure the role is set to admin:
UPDATE users 
SET password='<paste_generated_hash>', role='admin' 
WHERE email='admin@nexus.com';
