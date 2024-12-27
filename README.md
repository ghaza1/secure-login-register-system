# Secure Web App

This project is a web application developed for the final course project in Secure Software Development at Alexandria National University. The aim is to enhance the security of an existing web application or build one from scratch, focusing on implementing essential security features to protect the application and its users.

Features & Security Criteria
The web application will integrate the following security features to ensure robustness and protection against common vulnerabilities:

Input Validation: The application will use secure validation methods to ensure that all user inputs are properly sanitized and validated, preventing malicious input that could lead to attacks like SQL injection or XSS.

Password Hashing: We will implement a secure password storage mechanism using a strong hashing algorithm (e.g., bcrypt or SHA-256) to store user passwords securely.
SQL Injection Mitigation: The application will include prepared statements and parameterized queries to protect against SQL injection attacks.

Output Encoding: We will use output encoding techniques to prevent Cross-Site Scripting (XSS) attacks by ensuring that any user-generated content displayed on the page is properly sanitized.

Multi-Factor Authentication (MFA): Critical parts of the application will be secured with MFA, requiring users to verify their identity through an additional authentication method, such as a time-based OTP or a mobile app.

Buffer Overflow Protection: The application will implement strategies to mitigate buffer overflow vulnerabilities, ensuring that data handling is secure and protected from exploitation.

Access Control: Role-based access control (RBAC) will be used to restrict access to sensitive parts of the application based on user roles (e.g., Admin, User).

File Permissions and Monitoring: Proper file permissions will be enforced to limit unauthorized access, and security monitoring will be incorporated to detect potential vulnerabilities or breaches in the system.
