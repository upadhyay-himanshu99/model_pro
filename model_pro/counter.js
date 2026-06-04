// counter.js

let count = 0;

function increment() {
  count++;
  console.log("Count:", count);
}

function decrement() {
  count--;
  console.log("Count:", count);
}

function reset() {
  count = 0;
  console.log("Count reset to 0");
}

// Export functions
module.exports = {
  increment,
  decrement,
  reset
};