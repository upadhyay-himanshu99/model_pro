# Quick Start Guide - Full Stack Signup Application

## Prerequisites Checklist

- [ ] Node.js installed (v14+)
- [ ] npm installed
- [ ] MongoDB installed and service available
- [ ] VS Code open with this project

## Step-by-Step Setup

### 1️⃣ Install Backend Dependencies

Open a terminal in VS Code and run:

```bash
cd dashboard/backend
npm install
```

**What this does:**
- Installs Express (web framework)
- Installs Mongoose (MongoDB driver)
- Installs CORS (cross-origin support)

**Expected time:** 2-3 minutes  
**Expected message:** `added XX packages in XXs`

---

### 2️⃣ Install Frontend Dependencies  

Open a NEW terminal in VS Code and run:

```bash
cd dashboard/frontend
npm install
```

**What this does:**
- Installs React
- Installs React DOM
- Installs React Scripts build tools

**Expected time:** 3-5 minutes  
**Expected message:** `added XX packages in XXs`

---

### 3️⃣ Start MongoDB

Ensure MongoDB is running. On Windows:

```bash
net start MongoDB
```

Or start MongoDB server manually from Services.

**Verify:** Try connecting in MongoDB Compass to `mongodb://127.0.0.1:27017`

---

### 4️⃣ Start Backend Server

In your **first terminal**, run:

```bash
cd dashboard/backend
npm start
```

**Expected output:**
```
MongoDB Connected
Server running on port 5000
```

✅ **Do not close this terminal** - Backend must stay running!

---

### 5️⃣ Start Frontend Application

In your **second terminal**, run:

```bash
cd dashboard/frontend
npm start
```

**Expected:**
- Webpack will compile
- Browser opens to `http://localhost:3000`
- You see the Signup Form

✅ **Do not close this terminal** - Frontend development server must stay running!

---

## Test the Application

1. **Browser opens to:** `http://localhost:3000`
2. **Fill in the form:**
   - Name: `Arya`
   - Email: `arya@gmail.com`
   - Password: `123456`
3. **Click:** "Sign Up" button
4. **Expected Alert:** "User Registered Successfully"

---

## Verify in MongoDB

Open **MongoDB Compass** or MongoDB Shell:

```bash
mongo
```

```javascript
use dashboarddb
db.users.find().pretty()
```

**You should see:**
```json
{
  "_id": ObjectId("..."),
  "name": "Arya",
  "email": "arya@gmail.com",
  "password": "123456",
  "createdAt": ISODate("..."),
  "updatedAt": ISODate("...")
}
```

---

## Troubleshooting

### ❌ "npm: command not found"
- Node.js not installed or not in PATH
- **Fix:** Reinstall Node.js from nodejs.org
- Restart VS Code after installation

### ❌ "MongoDB Connection Failed"
- MongoDB service not running
- **Fix:** Run `net start MongoDB` in Command Prompt (as Admin)
- Or start MongoDB manually

### ❌ "Port 3000 already in use"
- Another React app is running
- **Fix:** Kill the process or use a different port
- ```bash
  # Windows
  netstat -ano | findstr :3000
  taskkill /PID <PID> /F
  ```

### ❌ "Port 5000 already in use"
- Another Express server is running
- **Fix:** Kill the process or use a different port
- ```bash
  # Windows
  netstat -ano | findstr :5000
  taskkill /PID <PID> /F
  ```

### ❌ "npm install takes too long"
- Slow internet or npm registry issues
- **Fix:** Try these alternatives:
  ```bash
  npm install --no-audit --no-fund
  npm install --legacy-peer-deps
  npm cache clean --force
  ```

### ❌ Form submission doesn't work
- Backend not running (check terminal 1)
- CORS error in browser console
- **Fix:** Verify backend is on port 5000 and responding
  ```bash
  # Test backend is running
  curl http://localhost:5000/
  # or open in browser
  ```

---

## File Overview

### Backend Structure
```
backend/
├── server.js          ← Main Express server
├── db/config.js       ← MongoDB connection
├── models/User.js     ← User database schema
├── package.json       ← Dependencies
└── node_modules/      ← Installed packages (after npm install)
```

### Frontend Structure
```
frontend/
├── src/
│   ├── App.js         ← Main React component
│   ├── SignUp.js      ← Signup form component
│   ├── App.css        ← Styles
│   ├── index.js       ← React entry point
│   └── SignUp.css
├── public/
│   └── index.html     ← HTML template
├── package.json       ← Dependencies
└── node_modules/      ← Installed packages (after npm install)
```

---

## Form Validation

The signup form validates:
- ✅ **Name** - Cannot be empty
- ✅ **Email** - Must contain @
- ✅ **Password** - Must be 6+ characters

Errors appear below each field until corrected.

---

## Architecture

```
USER BROWSER (localhost:3000)
        ↓↑
    REACT APP (Frontend)
        ↓↑ (HTTP POST to localhost:5000/signup)
  EXPRESS SERVER (localhost:5000) (Backend)
        ↓↑ (Mongoose ODM)
   MONGODB (localhost:27017)
        ↓↑
   dashboarddb.users collection
```

---

## Next Steps After Testing

✅ Great! Your full-stack app is working!

Consider adding:
- Password hashing (bcryptjs)
- Email validation
- Duplicate email checking
- Login functionality
- JWT authentication
- Form improvements

---

## Quick Command Reference

| Command | What It Does |
|---------|-------------|
| `npm install` | Install dependencies |
| `npm start` | Start development server |
| `npm run build` | Create production build |
| `node server.js` | Start backend directly |
| `net start MongoDB` | Start MongoDB service |

---

## Support

- Check browser console for errors (F12)
- Check terminal output for server errors
- Ensure all three services are running:
  - ✅ MongoDB (port 27017)
  - ✅ Backend (port 5000)
  - ✅ Frontend (port 3000)

---

**Congrats! You now have a working full-stack signup application!** 🎉
