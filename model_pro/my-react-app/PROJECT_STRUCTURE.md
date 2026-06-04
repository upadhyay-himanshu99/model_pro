# Project Structure & Architecture

## Complete Folder Layout

```
my-react-app/
│
├── SETUP.md                          ← You are here! Start with this file
├── README.md                         ← Detailed documentation
│
└── dashboard/
    │
    ├── backend/                      ← Express.js Server
    │   │
    │   ├── server.js                 ← Main server file (handles POST /signup)
    │   ├── package.json              ← Dependencies: express, mongoose, cors
    │   │
    │   ├── db/
    │   │   └── config.js             ← MongoDB connection (connectDB function)
    │   │
    │   ├── models/
    │   │   └── User.js               ← Mongoose User schema
    │   │
    │   ├── node_modules/             ← Generated after 'npm install'
    │   │   ├── express/
    │   │   ├── mongoose/
    │   │   └── cors/
    │   │
    │   └── install-backend.bat       ← Batch file for Windows npm install
    │
    └── frontend/                     ← React Application
        │
        ├── package.json              ← Dependencies: react, react-dom, react-scripts
        │
        ├── public/
        │   └── index.html            ← HTML template
        │
        ├── src/
        │   ├── index.js              ← React entry point
        │   ├── App.js                ← Root component
        │   ├── App.css               ← Root styling
        │   ├── SignUp.js             ← Signup form component
        │   └── SignUp.css            ← Form styling
        │
        ├── node_modules/             ← Generated after 'npm install'
        │   ├── react/
        │   ├── react-dom/
        │   └── react-scripts/
        │
        └── install-frontend.bat      ← Batch file for Windows npm install
```

---

## Data Flow Diagram

```
┌─────────────────────────────────────┐
│   USER ENTERS DATA IN BROWSER       │
│   http://localhost:3000             │
├─────────────────────────────────────┤
│  Name:  Arya                        │
│  Email: arya@gmail.com              │
│  Pass:  123456                      │
└───────────────┬─────────────────────┘
                │
                ▼
┌─────────────────────────────────────┐
│    REACT COMPONENT (SignUp.js)      │
├─────────────────────────────────────┤
│ • Collects form input               │
│ • Validates fields                  │
│ • Sends POST request to backend     │
└───────────────┬─────────────────────┘
                │
                │ HTTP POST to
                │ localhost:5000/signup
                │ {name, email, password}
                │
                ▼
┌─────────────────────────────────────┐
│  EXPRESS SERVER (server.js)         │
│  http://localhost:5000              │
├─────────────────────────────────────┤
│ • Receives POST request             │
│ • Extracts {name, email, password}  │
│ • Creates new User object           │
│ • Saves to database                 │
└───────────────┬─────────────────────┘
                │
                │ Mongoose.save()
                │ Creates user document
                │
                ▼
┌─────────────────────────────────────┐
│      MONGODB DATABASE               │
│   mongodb://127.0.0.1:27017         │
├─────────────────────────────────────┤
│  Database: dashboarddb              │
│  Collection: users                  │
│  Document: {                        │
│    _id: ObjectId(...),              │
│    name: "Arya",                    │
│    email: "arya@gmail.com",         │
│    password: "123456",              │
│    createdAt: ISODate(...),         │
│    updatedAt: ISODate(...)          │
│  }                                  │
└─────────────────────────────────────┘
                │
                │ Response JSON
                │ {message: "User..."}
                │
                ▼
┌─────────────────────────────────────┐
│    BROWSER SHOWS SUCCESS ALERT      │
│   "User Registered Successfully"    │
│    Form clears automatically        │
└─────────────────────────────────────┘
```

---

## Backend Server (server.js) Details

```javascript
// ============ IMPORTS ============
const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const connectDB = require('./db/config');
const User = require('./models/User');

// ============ SETUP ============
const app = express();
app.use(cors());              // Enable CORS
app.use(express.json());      // Parse JSON

// ============ STARTUP ============
connectDB();                  // Connect to MongoDB

// ============ ROUTES ============
POST /signup → Save user to database
GET  /       → Health check

// ============ LISTEN ============
app.listen(5000, () => {
  console.log('Server running on port 5000');
});
```

---

## Database Schema (models/User.js)

```javascript
{
  name: {
    type: String,
    required: true
  },
  email: {
    type: String,
    required: true
  },
  password: {
    type: String,
    required: true
  },
  createdAt: {
    type: Date,
    default: Date.now  // Automatic timestamp
  },
  updatedAt: {
    type: Date,
    default: Date.now  // Automatic timestamp
  }
}
```

---

## API Endpoint Reference

### POST /signup

**URL:** `http://localhost:5000/signup`

**Request Body:**
```json
{
  "name": "Arya",
  "email": "arya@gmail.com",
  "password": "123456"
}
```

**Success Response (201):**
```json
{
  "message": "User Registered Successfully"
}
```

**Error Response (500):**
```json
{
  "message": "Error registering user",
  "error": "error description"
}
```

---

## Component Hierarchy

```
App (App.js)
  └── SignUp (SignUp.js)
       └── form
            ├── input[name]
            ├── input[email]
            ├── input[password]
            └── button[submit]
```

---

## State Management (SignUp.js)

```javascript
const [formData, setFormData] = useState({
  name: '',
  email: '',
  password: ''
});

const [errors, setErrors] = useState({});
const [loading, setLoading] = useState(false);
```

---

## Validation Logic

```
NAME VALIDATION:
  Input: ""           → Error: "Name cannot be empty"
  Input: "Arya"       → ✅ Valid

EMAIL VALIDATION:
  Input: "arya"       → Error: "Email must contain @"
  Input: "a@"         → Error: "Email must contain @"
  Input: "a@b.com"    → ✅ Valid

PASSWORD VALIDATION:
  Input: "12345"      → Error: "Password must be at least 6 characters"
  Input: "123456"     → ✅ Valid
  Input: "mypass123"  → ✅ Valid
```

---

## Port Allocation

| Service | Port | URL |
|---------|------|-----|
| Frontend (React) | 3000 | http://localhost:3000 |
| Backend (Express) | 5000 | http://localhost:5000 |
| Database (MongoDB) | 27017 | mongodb://127.0.0.1:27017 |

---

## Dependencies Summary

### Backend (Node.js)
- **express** - Web framework for routing
- **mongoose** - MongoDB ODM (Object Data Model)
- **cors** - Cross-Origin Resource Sharing middleware

### Frontend (React)
- **react** - UI library
- **react-dom** - React rendering
- **react-scripts** - Build tools and dev server

---

## Error Handling

### Frontend
- Form validation errors show below each field
- Network errors show in alert dialog
- Loading state prevents multiple submissions

### Backend
- MongoDB connection errors halt server startup
- Request validation with try-catch blocks
- Error responses include error details

---

## Security Considerations

⚠️ **Note:** This is a learning project. For production:

- [ ] Hash passwords with bcryptjs
- [ ] Validate email format
- [ ] Check for duplicate emails
- [ ] Use environment variables
- [ ] Add rate limiting
- [ ] Implement authentication
- [ ] Use HTTPS instead of HTTP
- [ ] Add input sanitization
- [ ] Implement CSRF protection

---

## Quick Reference

### File Purposes

| File | Purpose |
|------|---------|
| server.js | Express setup, routes, server startup |
| db/config.js | MongoDB connection configuration |
| models/User.js | Mongoose schema definition |
| App.js | Root React component |
| SignUp.js | Form component with validation logic |
| package.json | Dependency declaration |
| index.html | HTML document structure |

### Key Functions

| Function | File | Purpose |
|----------|------|---------|
| connectDB() | db/config.js | Connect to MongoDB |
| new User() | models/User.js | Create user document |
| user.save() | server.js | Save to database |
| fetch() | SignUp.js | Send POST request |
| useState() | SignUp.js | Manage component state |

---

## Running the Application

### Terminal 1 (Backend)
```bash
cd dashboard/backend
npm start
# Output: MongoDB Connected
#         Server running on port 5000
```

### Terminal 2 (Frontend)
```bash
cd dashboard/frontend
npm start
# Output: Opens browser to localhost:3000
```

### Terminal 3 (Optional - MongoDB Shell)
```bash
mongo
use dashboarddb
db.users.find().pretty()
```

---

## Success Indicators

✅ All files created (13 files)  
✅ Folder structure complete  
✅ Backend server starts without errors  
✅ Frontend compiles without warnings  
✅ Form validation works  
✅ Data saves to MongoDB  
✅ Alert shows success message  
✅ Data visible in MongoDB Compass  

---

**You're ready to go! Start with the SETUP.md file next.** 🚀
