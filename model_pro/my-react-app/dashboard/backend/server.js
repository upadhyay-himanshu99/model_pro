const express = require('express');
const mongoose = require('mongoose');
const cors = require('cors');
const connectDB = require('./db/config');
const User = require('./models/User');

const app = express();

// Enable CORS and JSON parsing
app.use(cors());
app.use(express.json());

// Connect to MongoDB
connectDB();

// POST /signup route
app.post('/signup', async (req, res) => {
  try {
    const { name, email, password } = req.body;

    // Create a new user
    const user = new User({
      name,
      email,
      password
    });

    // Save user to database
    await user.save();

    res.status(201).json({ message: 'User Registered Successfully' });
  } catch (error) {
    console.error('Error registering user:', error);
    res.status(500).json({ message: 'Error registering user', error: error.message });
  }
});

// Basic route to check server status
app.get('/', (req, res) => {
  res.json({ message: 'Backend server is running' });
});

// Start server
const PORT = 5000;
app.listen(PORT, () => {
  console.log(`Server running on port ${PORT}`);
});
