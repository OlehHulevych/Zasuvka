# Product Documentation: Zasuvka

## Product Overview
**Vision:** Zasuvka is a website where users can buy second-hand goods at a set price, and also sell their own items to earn money.

**Main problem:** Users can buy cheaper items and sell things they no longer need.

**Target audience:** Individuals, companies, online shops.

---

## Functional Requirements
- **Profile management:** Registration and login (email).
- **Main functionality:** Users can add, edit, or delete their own listings (ads).
- **Search and filtering:** Ability to find items by categories or by a specific keyword.

---

## User Roles
- **Admin:** Has full access to user management and the database.
- **Regular user:** Can view and edit only their own content.
- **Guest:** Can only browse public parts of the website and view listings.

---

## Technical Side
- **Frontend:** HTML, CSS, JavaScript
- **Backend:** PHP, JSON
- **Responsiveness:** The application must work on mobile, tablet, and desktop.
- **Security:** Data encryption.

---

## Functionality
When a user visits our website, they will see the main page of our e-shop.

### Home Page
Here the user will see:
- a search field,
- search by categories,
- buttons for favorite listings and login,
- current promotions on our e-shop.

**Home page preview:**
![Home page](https://github.com/user-attachments/assets/c77a68f4-6466-461b-b53a-03e160baf502)

The user can browse listings from other users. They can view:
- photos,
- description,
- price,
- contacts.

**Listing preview:**
![Listing preview](https://github.com/user-attachments/assets/da9e3749-05d5-43b2-919e-b6691778b84d)

---

### Login and Registration
If the user wants to create their own listings, they must register or log in.

**Registration requirements:**
- name
- phone number
- email
- password
- profile photo upload

**Login requirements:**
- email
- password

If something is missing, the application will show the reason why the user cannot log in or why the account cannot be created.

**Registration / Login screens:**
![Registration](https://github.com/user-attachments/assets/de68daaf-6fbe-4d93-ac05-356ecc5339f6)
![Login](https://github.com/user-attachments/assets/7df76d92-a327-4226-9f3f-33b4ac31cdcc)

---

### Creating Listings
If the user is logged in, they can create their own listings.

To create a listing, the user must enter:
- name/title
- price
- location
- category
- description
- images upload

If something is missing, the application will show exactly what is missing.

**Create listing screen:**
![Create listing](https://github.com/user-attachments/assets/83253193-2b31-41b2-bfb9-1e05fc99eddf)

---

### Editing the Profile and Listings
If the user doesn’t like something, they can always edit their listings and even their profile.

They can:
- add new information
- delete existing information

**Edit screens:**
![Edit profile](https://github.com/user-attachments/assets/c8073376-ed6b-4fe8-a5b7-f2a48422a1e9)
![Edit listing](https://github.com/user-attachments/assets/1bdc8708-9776-4349-996b-493c22f6bb60)

---

### Admin Panel
The e-shop administrator (or a person who received permissions from the administrator) has access to the admin panel.

The admin can:
- view users
- increase or decrease user roles
- delete a user account
- view listings
- delete listings

**Admin panel preview:**
![Admin panel](https://github.com/user-attachments/assets/24e3dabe-e353-431b-b97c-2bc031ae45a7)
