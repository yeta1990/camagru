# Camagru - A Photo Sharing Application - Technical Documentation

## Overview
This project is a photo-sharing web application inspired by platforms like Instagram. Users can upload, edit, and share images with the community. It emphasizes modular design, security, and reusability, ensuring smooth user interaction and developer flexibility.

![image](https://github.com/user-attachments/assets/a4b08d25-b9e5-465e-a002-c98af70811af)
![image](https://github.com/user-attachments/assets/59f83c1b-2c93-4a5c-afe7-e332816f1197)


---

## Architecture
### Backend

- RESTful API:
  - Developed in PHP with a modular, MVC-inspired design.
  - Each endpoint follows a simple declaration format for extensibility.
- Authentication: implements JWT-based token authentication without external libraries.
- Database: 
  - Lightweight and embedded database solution with SQLite3.
  - Managed by a generic DbService class offering ORM-like features for object serialization and database interaction.
- Security:
  - DDoS protection using mod_evasive.
  - Validations for SQL injection, CSRF, and XSS attacks.
- Email Service: Mailjet integration for transactional emails

### Frontend

- Technology Stack: Vanilla JavaScript, HTML, CSS
- Pico.css as the only external dependency for styling.
- Features: Responsive design, dark mode support


## Design Considerations
- Microframework Approach:
  - Backend designed for reusability and scalability.
  - New endpoints can be added with minimal configuration.
- Security by Design:
  - No sensitive data is stored in the repository; .env files handle environment variables such as API keys.
  - Default authentication requirement with whitelist support
- Scalability: Modular service-based architecture supports the addition of new features with minimal impact on existing code.
- Routing System:
  - URL rewriting with .htaccess
  - API route prefix (/api/) handling
  - Automatic routing to HTML views
  - Simple route definition system


-----

## Features
### User Features
- Authentication:
  - Password-based login with email link verification.
  - Password recovery via email using a unique JWT token.
- Profile Management: edit username, email, or password after logging in. Enable or disable notifications by email.

### Gallery Features
- Public Image Feed:
  - Displays all user-edited images in chronological order.
  - Supports pagination with at 5 items per page.
- Interactions:
  - Users can like and comment on images.
  - Email notifications sent to image authors when new comments are posted (can be disabled in preferences).

### Photo Editing Features
- Image Capture:
  - Use the webcam or upload images from the local device.
  - Select predefined overlay images to merge with user photos.
- Editing Interface:
  - Preview the webcam feed alongside overlay options.
  - Capture button is enabled only after selecting an overlay image.
- Server-Side Processing: final image generation (superimposing overlays) is handled on the backend.
- Users can delete their own images.

### Additional Features
- Responsive Design: ensures usability on devices of varying screen sizes, including desktops and mobile phones.
- Dark Mode: available to enhance usability and reduce eye strain in low-light environments.


## Deployment
### Prerequisites
Docker and Docker Compose installed on the host system.

### Environment Setup
1. Configure the .env file with required variables:

```
MAILJET_USER=''
MAILJET_PASS=''
JWT_PASS=''
JWT_RECOVER=''
```

2. Running the Application
Start the application with:
```
docker-compose up
```
3. Access the application at http://localhost:8080.
