# 🚀 Quick Start Reference

## Before You Begin

✅ Have you read **SETUP.md**? If not, go read it now!

---

## ⚡ 3-Command Quick Start

### Terminal 1 - Backend
```bash
cd dashboard/backend
npm install
npm start
```
**Expected Output:**
```
MongoDB Connected
Server running on port 5000
```

### Terminal 2 - Frontend
```bash
cd dashboard/frontend
npm install
npm start
```
**Expected:** Browser opens to `http://localhost:3000`

### MongoDB (already running?)
```bash
net start MongoDB
```

---

## 📝 Test Submission

**Form Input:**
```
Name:     Arya
Email:    arya@gmail.com
Password: 123456
```

**Expected Result:**
```
✅ Alert: "User Registered Successfully"
✅ Form clears
✅ Data in MongoDB Compass
```

---

## 🔍 Verify in MongoDB Compass

**Connection String:** `mongodb://127.0.0.1:27017`

**Database:** dashboarddb  
**Collection:** users  

**Expected Document:**
```json
{
  "_id": ObjectId("..."),
  "name": "Arya",
  "email": "arya@gmail.com",
  "password": "123456"
}
```

---

## 🎯 Success Checklist

- [ ] Backend terminal: "MongoDB Connected"
- [ ] Backend terminal: "Server running on port 5000"
- [ ] Frontend: Browser opens to localhost:3000
- [ ] Form validation works
- [ ] Submit shows alert: "User Registered Successfully"
- [ ] Form clears after submit
- [ ] MongoDB Compass shows data

---

## 🐛 Quick Troubleshooting

| Issue | Fix |
|-------|-----|
| MongoDB error | `net start MongoDB` |
| npm install fails | `npm install --legacy-peer-deps` |
| Port 3000 in use | Different React app running - stop it |
| Port 5000 in use | Different server running - stop it |
| Form won't submit | Check browser console (F12) |
| No data in DB | Backend might not be running |

---

## 📂 Files at a Glance

**Most Important Files:**
- `server.js` - Backend logic
- `SignUp.js` - Frontend form
- `User.js` - Database model
- `config.js` - Database connection

**To Edit:**
- Form fields? → Edit `SignUp.js`
- Validation? → Edit `SignUp.js` 
- API endpoint? → Edit `server.js`
- Database fields? → Edit `User.js`

---

## 🔗 URLs

- **Frontend**: http://localhost:3000
- **Backend Health**: http://localhost:5000/
- **MongoDB**: mongodb://127.0.0.1:27017

---

## 📱 Form Fields

```
┌─────────────────────────┐
│ SIGNUP FORM             │
├─────────────────────────┤
│ Name: [____________]    │
│ Email: [____________]   │
│ Password: [____________]│
├─────────────────────────┤
│  [  Sign Up  ]          │
└─────────────────────────┘
```

---

## 💾 Backend Endpoint

```
POST /signup
Content-Type: application/json

{
  "name": "string",
  "email": "string",
  "password": "string"
}

Response:
{
  "message": "User Registered Successfully"
}
```

---

## ✅ All 28 Files Created

### Backend (13 files)
- ✅ server.js
- ✅ package.json
- ✅ db/config.js
- ✅ models/User.js
- ✅ install-backend.bat
- ✅ node_modules/ (after npm install)

### Frontend (10 files)
- ✅ App.js
- ✅ SignUp.js
- ✅ index.js
- ✅ App.css
- ✅ SignUp.css
- ✅ public/index.html
- ✅ package.json
- ✅ install-frontend.bat
- ✅ node_modules/ (after npm install)

### Documentation (5 files)
- ✅ SETUP.md
- ✅ PROJECT_STRUCTURE.md
- ✅ README.md
- ✅ CHECKLIST.md
- ✅ BUILD_SUMMARY.md
- ✅ QUICK_REFERENCE.md (this file)

---

## 📊 Architecture Overview

```
USER BROWSER (3000)
       ↕
   REACT APP
       ↕
EXPRESS SERVER (5000)
       ↕
   MONGODB (27017)
```

---

## 🚀 Start Here

1. Open `SETUP.md` ← Start with this!
2. Follow the installation steps
3. Run both servers
4. Test the form
5. Check MongoDB
6. Celebrate! 🎉

---

## 📞 Documentation Quick Links

- **Getting Started** → Open SETUP.md
- **Architecture** → Open PROJECT_STRUCTURE.md
- **Full Reference** → Open README.md
- **Testing** → Open CHECKLIST.md
- **What Was Built** → Open BUILD_SUMMARY.md

---

## 💡 Remember

- Terminal 1: Keep backend running
- Terminal 2: Keep frontend running
- MongoDB: Must be running
- Don't close terminals during testing

---

**Status: ✅ Ready to Use**  
**Next: Read SETUP.md and start!**

🎯 Go to SETUP.md now! →
