# Full Stack Signup Application

A complete React + Express + MongoDB signup system where user data entered in a React form is stored in MongoDB.

## Project Structure

```
my-react-app/
│
├── dashboard/
│   │
│   ├── backend/
│   │   ├── db/
│   │   │   └── config.js          # MongoDB connection configuration
│   │   ├── models/
│   │   │   └── User.js            # Mongoose User model
│   │   ├── server.js              # Express server & routes
│   │   ├── package.json           # Backend dependencies
│   │   └── node_modules/          # (generated after npm install)
│   │
│   └── frontend/
│       ├── src/
│       │   ├── App.js             # Main React component
│       │   ├── App.css            # Styling
│       │   ├── SignUp.js          # Signup form component
│       │   ├── SignUp.css         # Form styling
│       │   └── index.js           # React entry point
│       ├── public/
│       │   └── index.html         # HTML template
│       ├── package.json           # Frontend dependencies
│       └── node_modules/          # (generated after npm install)
```

## Technology Stack

- **Frontend**: React, JavaScript, CSS
- **Backend**: Node.js, Express.js
- **Database**: MongoDB, Mongoose
- **Middleware**: CORS
- **Port**: Frontend (3000), Backend (5000), Database (27017)

## Setup Instructions

### Prerequisites

- Node.js and npm installed
- MongoDB server running locally
- VS Code or any code editor

### Step 1: Start MongoDB

Ensure MongoDB is running on `mongodb://127.0.0.1:27017`

```bash
# If using MongoDB Community Edition (Windows)
net start MongoDB
```

### Step 2: Install Backend Dependencies

```bash
cd dashboard/backend
npm install
```

Expected output:
```
npm notice
npm notice New minor version of npm available: X.X.X → Y.Y.Y
npm notice to update run: `npm install -g npm@Y.Y.Y`
npm notice
added XX packages in XXs
```

### Step 3: Start Backend Server

```bash
cd dashboard/backend
npm start
# or
node server.js
```

Expected output:
```
MongoDB Connected
Server running on port 5000
```

### Step 4: Install Frontend Dependencies (in a new terminal)

```bash
cd dashboard/frontend
npm install
```

### Step 5: Start Frontend Application

```bash
cd dashboard/frontend
npm start
```

This will automatically open the application in your browser at `http://localhost:3000`

## Testing the Application

### Test Data

1. Open the Signup page (should be at `http://localhost:3000`)
2. Enter the following information:
   - **Name**: Arya
   - **Email**: arya@gmail.com
   - **Password**: 123456
3. Click the "Sign Up" button

### Expected Results

#### Frontend
- An alert should appear with message: **"User Registered Successfully"**
- Form fields should clear after successful submission

#### Backend Console
- You should see the POST request logged
- No errors should appear

#### MongoDB
- Open MongoDB Compass or use mongo shell
- Navigate to `dashboarddb` → `users` collection
- You should see the new user document:

```json
{
  "_id": ObjectId("..."),
  "name": "Arya",
  "email": "arya@gmail.com",
  "password": "123456",
  "createdAt": ISODate("2026-06-02T..."),
  "updatedAt": ISODate("2026-06-02T...")
}
```

## Form Validation Rules

The frontend validates:
- ✅ **Name**: Cannot be empty
- ✅ **Email**: Must contain '@' symbol
- ✅ **Password**: Must be at least 6 characters long

Invalid submissions show error messages below each field.

## File Descriptions

### Backend Files

**server.js**
- Express server setup
- CORS and JSON middleware enabled
- MongoDB connection at startup
- POST `/signup` route that saves users to database
- Error handling and response messages

**db/config.js**
- MongoDB connection configuration
- Uses Mongoose to connect
- Connection string: `mongodb://127.0.0.1:27017/dashboarddb`
- Exports `connectDB()` function

**models/User.js**
- Mongoose schema for User collection
- Fields: `name`, `email`, `password`
- Timestamps added automatically
- Exported as Mongoose model

### Frontend Files

**App.js**
- Root React component
- Imports and renders SignUp component
- Simple layout wrapper

**SignUp.js**
- Main signup form component
- Uses `useState` for form state management
- Validates form input
- Sends POST request to backend on submit
- Displays success/error alerts
- Clears form on successful submission

**App.css & SignUp.css**
- Responsive design
- Modern gradient background
- Form styling with error states
- Button hover effects

## API Endpoint

### POST /signup

**Request:**
```json
{
  "name": "string",
  "email": "string",
  "password": "string"
}
```

**Response (Success - 201):**
```json
{
  "message": "User Registered Successfully"
}
```

**Response (Error - 500):**
```json
{
  "message": "Error registering user",
  "error": "error details"
}
```

## Troubleshooting

### MongoDB Connection Error
- Ensure MongoDB service is running
- Check connection string: `mongodb://127.0.0.1:27017/dashboarddb`
- Look for "MongoDB Connected" message in server console

### CORS Error
- Backend must have `app.use(cors())` enabled
- Frontend proxy is set to `http://localhost:5000` in package.json
- Ensure backend is running on port 5000

### Port Already in Use
- Frontend (3000): Kill process or use `netstat -ano | findstr :3000`
- Backend (5000): Kill process or use `netstat -ano | findstr :5000`

### npm install Timeout
- Try: `npm install --no-audit --no-fund`
- Or: `npm install --legacy-peer-deps`
- Check internet connection

## Features

✅ Form validation with real-time error messages  
✅ Password type input field  
✅ Responsive design  
✅ Loading state on submit button  
✅ Error messages clear on user input  
✅ Automatic database timestamp fields  
✅ CORS enabled for cross-origin requests  
✅ Clean, modern UI with gradient design  

## Development Notes

- Backend runs on port **5000**
- Frontend runs on port **3000**
- MongoDB connection string: `mongodb://127.0.0.1:27017/dashboarddb`
- Database name: `dashboarddb`
- Collection name: `users` (auto-created by MongoDB)

## Future Enhancements

- Password hashing with bcryptjs
- Email validation
- Duplicate email checking
- Login functionality
- User authentication with JWT
- Database validation and error handling
- Environment variables for configuration
- User profile page
- Admin dashboard

---

**Created**: June 2, 2026  
**Stack**: React + Express + MongoDB + Mongoose + CORS
