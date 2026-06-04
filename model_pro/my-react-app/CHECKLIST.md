# Setup & Testing Checklist

## ✅ Pre-Setup Requirements

- [ ] Node.js installed (v14 or higher)
  - Verify: `node --version`
- [ ] npm installed
  - Verify: `npm --version`
- [ ] MongoDB installed
  - Verify: `mongo --version`
- [ ] VS Code open with project
- [ ] Internet connection (for downloading packages)

---

## 📁 File Structure Verification

- [ ] `my-react-app/dashboard/backend/server.js` exists
- [ ] `my-react-app/dashboard/backend/db/config.js` exists
- [ ] `my-react-app/dashboard/backend/models/User.js` exists
- [ ] `my-react-app/dashboard/backend/package.json` exists
- [ ] `my-react-app/dashboard/frontend/src/App.js` exists
- [ ] `my-react-app/dashboard/frontend/src/SignUp.js` exists
- [ ] `my-react-app/dashboard/frontend/public/index.html` exists
- [ ] `my-react-app/dashboard/frontend/package.json` exists

---

## 🔧 Installation Steps

### MongoDB Setup
- [ ] Start MongoDB service
  - Command: `net start MongoDB` (Windows Admin)
  - Or manually start via Services
- [ ] Verify MongoDB is running
  - Open MongoDB Compass
  - Should connect to `mongodb://127.0.0.1:27017`

### Backend Installation
- [ ] Open Terminal 1
- [ ] Navigate: `cd dashboard/backend`
- [ ] Install: `npm install`
  - Expected time: 2-3 minutes
  - Look for: `added XX packages`
- [ ] Verify: `node_modules` folder created
- [ ] Test: `npm start`
  - Expected output:
    ```
    MongoDB Connected
    Server running on port 5000
    ```
- [ ] ✅ Leave Terminal 1 running!

### Frontend Installation
- [ ] Open Terminal 2 (new terminal)
- [ ] Navigate: `cd dashboard/frontend`
- [ ] Install: `npm install`
  - Expected time: 3-5 minutes
  - Look for: `added XX packages`
- [ ] Verify: `node_modules` folder created
- [ ] Test: `npm start`
  - Expected: Browser opens to `http://localhost:3000`
  - React Signup form should display
- [ ] ✅ Leave Terminal 2 running!

---

## 📝 Form Validation Testing

### Test Case 1: Empty Name
- [ ] Clear name field
- [ ] Enter email: `test@test.com`
- [ ] Enter password: `password123`
- [ ] Click "Sign Up"
- [ ] ✅ Should see error: "Name cannot be empty"

### Test Case 2: Invalid Email
- [ ] Enter name: `Test User`
- [ ] Enter email: `invalidemail` (no @)
- [ ] Enter password: `password123`
- [ ] Click "Sign Up"
- [ ] ✅ Should see error: "Email must contain @"

### Test Case 3: Short Password
- [ ] Enter name: `Test User`
- [ ] Enter email: `test@test.com`
- [ ] Enter password: `123` (less than 6 chars)
- [ ] Click "Sign Up"
- [ ] ✅ Should see error: "Password must be at least 6 characters"

### Test Case 4: Valid Submission
- [ ] Enter name: `Arya`
- [ ] Enter email: `arya@gmail.com`
- [ ] Enter password: `123456`
- [ ] Click "Sign Up"
- [ ] ✅ Should see alert: "User Registered Successfully"
- [ ] ✅ Form should clear automatically

---

## 🗄️ MongoDB Verification

### Using MongoDB Compass
- [ ] Open MongoDB Compass
- [ ] Connect to `mongodb://127.0.0.1:27017`
- [ ] Look for database: `dashboarddb`
- [ ] Look for collection: `users`
- [ ] Should see document:
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

### Using MongoDB Shell
- [ ] Open Terminal 3
- [ ] Type: `mongo`
- [ ] Type: `use dashboarddb`
- [ ] Type: `db.users.find().pretty()`
- [ ] ✅ Should see user document with Arya's data

---

## 🧪 Integration Testing

### End-to-End Test (Complete Flow)

1. [ ] All three services running:
   - Terminal 1: Backend (port 5000) - `MongoDB Connected`
   - Terminal 2: Frontend (port 3000) - Browser open
   - MongoDB service - Running

2. [ ] Submit valid form:
   - Name: `Arya`
   - Email: `arya@gmail.com`
   - Password: `123456`

3. [ ] Frontend response:
   - [ ] Alert shows: "User Registered Successfully"
   - [ ] Form clears
   - [ ] No console errors (F12)

4. [ ] Backend response:
   - [ ] Terminal 1 shows POST request logged
   - [ ] No error messages
   - [ ] Server still running

5. [ ] Database response:
   - [ ] MongoDB Compass shows new document
   - [ ] Data matches form submission
   - [ ] Timestamps are present

---

## 🐛 Troubleshooting Checklist

### If form doesn't submit:
- [ ] Check browser console (F12) for errors
- [ ] Verify backend is running on port 5000
- [ ] Verify `http://localhost:5000/` responds
- [ ] Check network tab for failed requests
- [ ] Ensure CORS is enabled in backend

### If MongoDB connection fails:
- [ ] Check MongoDB service is running
- [ ] Verify connection string: `mongodb://127.0.0.1:27017`
- [ ] Open MongoDB Compass to test connection
- [ ] Check firewall settings
- [ ] Ensure port 27017 is open

### If npm install fails:
- [ ] Clear npm cache: `npm cache clean --force`
- [ ] Try: `npm install --legacy-peer-deps`
- [ ] Try: `npm install --no-audit --no-fund`
- [ ] Check internet connection
- [ ] Verify Node.js version is 14+

### If port is already in use:
- [ ] Windows command: `netstat -ano | findstr :3000`
- [ ] Kill process: `taskkill /PID <PID> /F`
- [ ] Or change port in package.json

### If React won't compile:
- [ ] Check console for syntax errors
- [ ] Ensure all files are saved
- [ ] Try: `npm start` again
- [ ] Clear browser cache (Ctrl+Shift+Delete)
- [ ] Restart terminal

---

## 📊 Testing Summary

### Backend Tests
- [ ] Server starts without errors
- [ ] MongoDB connection successful
- [ ] POST /signup route responds
- [ ] Data saves to database
- [ ] Success message returned

### Frontend Tests
- [ ] App loads in browser
- [ ] Form displays all fields
- [ ] Validation works for all fields
- [ ] Form submits successfully
- [ ] Success alert appears
- [ ] Form clears after submit

### Database Tests
- [ ] Database created: `dashboarddb`
- [ ] Collection created: `users`
- [ ] Document inserted with correct data
- [ ] Timestamps added automatically
- [ ] Can query data with MongoDB tools

---

## ✨ Success Indicators

You'll know everything is working when:

✅ Terminal 1 shows: `MongoDB Connected` and `Server running on port 5000`  
✅ Terminal 2 shows React app in browser  
✅ Form validation prevents invalid submissions  
✅ Valid form shows success alert  
✅ MongoDB Compass shows new user data  
✅ No error messages in browser console  
✅ No error messages in backend terminal  

---

## 🎯 Next Steps

Once testing is complete:

1. [ ] Review the code to understand data flow
2. [ ] Experiment with different data
3. [ ] Check MongoDB for multiple users
4. [ ] Try to break the validation
5. [ ] Read PROJECT_STRUCTURE.md for deep dive
6. [ ] Consider enhancements:
   - [ ] Password hashing with bcryptjs
   - [ ] Email validation
   - [ ] Duplicate email checking
   - [ ] Login functionality
   - [ ] User profile page

---

## 📞 Quick Help

| Problem | Solution |
|---------|----------|
| Ports in use | Kill process with taskkill or change ports |
| MongoDB won't connect | Start service: `net start MongoDB` |
| npm install slow | `npm install --no-audit --no-fund` |
| Form won't submit | Check F12 console for errors |
| No data in DB | Verify backend saved (check logs) |

---

## 🎉 Celebration Checklist

Once everything works:

- [ ] Take a screenshot of the success alert
- [ ] Take a screenshot of MongoDB Compass with data
- [ ] Share your accomplishment!
- [ ] Review code to learn
- [ ] Plan enhancements

---

**Created: June 2, 2026**  
**Status: Ready for Testing** ✅  
**Last Updated: Setup Complete**

You have successfully set up a full-stack application! 🚀
