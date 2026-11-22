# 🌍 Day With Explore
### Local Visit Planning and Information System

**A Web-Based Tourist Information and One-Day Trip Planning Application**

Day With Explore is a tourism web application designed to help local and international tourists discover attractions and plan one-day trips around **Eravur and Batticaloa, Sri Lanka**.

The application brings tourist destination information, search and filtering, maps, nearby facilities, and trip planning together in one platform.

---

## 📌 Project Information

| Details | Information |
|---|---|
| Project Name | Day With Explore |
| Project Type | Tourism Web Application |
| Module | ITE2953 – Programming Group Project |
| University | University of Moratuwa |
| Degree | Bachelor of Information Technology (External) |
| Developer | I. Amras Ahamed |
| Registration Number | E2410946 |
| Development Environment | XAMPP / Apache |
| Database | SQLite |

## 🎯 Project Objectives

The main objectives of this project are to:

- Provide information about tourist attractions within approximately 25 km of Eravur.
- Help tourists search and filter destinations.
- Display destination descriptions, photographs, opening hours, entrance fees, and locations.
- Integrate Google Maps for location and route guidance.
- Provide information about nearby facilities.
- Allow users to create and manage a one-day visit plan.
- Provide an administrator interface for managing tourist destinations.

## ✨ Main Features

### 🏠 1. Homepage

The homepage introduces the application and provides navigation to its main features.

### 🏖️ 2. Tourist Attractions

Users can browse tourist attractions around Eravur and Batticaloa, including beaches, religious places, historical sites, natural attractions, and cultural destinations.

### 🔎 3. Search and Category Filtering

Tourists can search for destinations by name and filter places according to their preferred categories.

### 📍 4. Destination Details

The destination details section provides useful visitor information, including descriptions, photographs, locations, opening hours, entrance fees, contact information, and distances where available.

### 🗺️ 5. Google Maps Integration

Google Maps helps users view tourist attraction locations and obtain route guidance.

### 🏥 6. Nearby Facilities

The application provides information about useful nearby services, such as restaurants, ATMs, hospitals, and parking facilities.

### 📅 7. One-Day Visit Planner

Users can select multiple destinations and organize a one-day travel plan.

The planner supports adding destinations, changing the visiting order, and removing selected places.

### 🔐 8. Admin Dashboard

Authorized administrators can manage tourism information through the admin dashboard.

Main operations include:

- Add new tourist destinations.
- Edit existing destination information.
- Delete destination records.
- View and manage tourism information.

---

## 🛠️ Technologies Used

| Technology | Purpose |
|---|---|
| HTML5 | Web page structure |
| CSS3 | Styling and layout |
| JavaScript | Interactive functionality |
| Bootstrap 5 | Responsive web design |
| PHP | Backend development |
| SQLite | Database management |
| Google Maps Platform | Location and route guidance |
| XAMPP / Apache | Local web server |
| Visual Studio Code | Code development |
| Git and GitHub | Version control |

---

## 🚀 How to Run the Project Locally

Follow these steps to run Day With Explore on your computer.

### Step 1 – Install XAMPP

Download and install XAMPP:

https://www.apachefriends.org/

Make sure Apache and PHP are available.

### Step 2 – Download the Project

Clone the GitHub repository:

```bash
git clone https://github.com/ahamedamras/YOUR-REPOSITORY-NAME.git
```

Replace `YOUR-REPOSITORY-NAME` with the actual repository name.

Alternatively, download the repository as a ZIP file and extract it.

### Step 3 – Move the Project Folder

Copy the project folder into the XAMPP `htdocs` directory.

Example for Windows:

```text
C:\xampp\htdocs\day-with-explore\
```

The folder should contain the application's PHP files, assets, configuration files, and other required project resources.

### Step 4 – Configure the SQLite Database

This application uses SQLite to store tourism information.

1. Locate the SQLite database file included with the project.
2. Check the database path in the PHP database connection configuration.
3. Make sure the database file is accessible to PHP.
4. Confirm that the PHP SQLite extension is enabled.
5. Ensure the application has the necessary database permissions.

The exact database filename and path depend on the repository's configuration.

**Note:** A separate MySQL server is not required for the SQLite database.

### Step 5 – Start Apache

1. Open the XAMPP Control Panel.
2. Click **Start** next to Apache.
3. Wait until Apache is running.

### Step 6 – Open the Website

Open your browser and enter:

```text
http://localhost/day-with-explore/index.php
```

The Day With Explore homepage should appear if the project is configured correctly.

### Step 7 – Test the Application

After opening the website, test the following functions:

1. Open the homepage.
2. Browse tourist attractions.
3. Search for a destination.
4. Filter attractions by category.
5. View destination details.
6. Open Google Maps and check location guidance.
7. Browse nearby facilities.
8. Add destinations to the one-day visit planner.
9. Reorder or remove destinations.
10. Log in as an administrator and test destination management.

**Important:** Google Maps features may require a valid API key and internet access, depending on the application's configuration.

---

## 🗄️ Database

The application uses SQLite for managing structured tourism information.

The main data entities described in the project design include:

- Destinations
- Categories
- Nearby Facilities
- Visit Plans
- Plan Destinations
- Administrators

PHP handles database operations, including retrieving destinations, applying category filters, and managing destination records.

---

## 🧩 System Design

The project includes five main system design diagrams:

1. Use Case Diagram
2. Activity Diagram
3. Sequence Diagram
4. Class Diagram
5. Entity Relationship Diagram (ERD)

These diagrams describe user interactions, application workflows, system communication, data structures, and database relationships.

---

## 🧪 Testing

The application was evaluated using functional, security, and non-functional testing.

The final project report summarizes the testing results as follows:

| Test Results | Reported Value |
|---|---:|
| Total Tests | 30 |
| Passed | 27 |
| Failed | 3 |
| Pass Rate | 90% |

These figures are taken from the final report's testing summary.

---

## 📸 Application Screenshots

Add screenshots from the implemented application to a `screenshots` folder in the repository.

Recommended screenshots:

- Homepage
- Tourist Attractions Listing
- Category Filtering
- Destination Details
- Google Maps
- Nearby Facilities
- One-Day Visit Planner
- Admin Dashboard

These screenshots help visitors understand the application before installing it.

---

## 🔮 Future Improvements

Possible future enhancements include:

- Sinhala and Tamil language support.
- Real-time weather information.
- Advanced route planning.
- Tourist registration and user accounts.
- Saving and managing multiple travel plans.
- Mobile application support.
- Online booking features.

---

## 👨‍💻 Developer

**I. Amras Ahamed**

Bachelor of Information Technology (External Degree)  
University of Moratuwa, Sri Lanka

**GitHub:** https://github.com/ahamedamras

**LinkedIn:** https://www.linkedin.com/in/amras-ahamed-1bb6b52b5

---

## 📚 Academic Project

This project was developed for the **ITE2953 – Programming Group Project** module at the University of Moratuwa.

It demonstrates practical knowledge of web application development, backend programming, relational databases, system analysis and design, software testing, and API integration.

---

**Day With Explore — Discover Places. Plan Your Day. Explore More.**
