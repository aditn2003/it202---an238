const mysql = require('mysql2');

// Database configuration
const dbConfig = {
  host: 'localhost',
  user: 'root',
  password: 'sqlpassword',
  database: 'classicmodels',
};

// Create a connection to the database
const connection = mysql.createConnection(dbConfig);

// Connect to the database
connection.connect((err) => {
  if (err) {
    console.error('Error connecting to the database:', err);
    return;
  }
  console.log('Connected to the database!');

  // Perform database operations
  performDatabaseOperations();

  // Close the connection when done
  connection.end();
});
let orderNumberArray = [];
let orderDateArray = [];

// Perform database operations
function performDatabaseOperations() {
  // Example: Select all rows from an 'orders' table
  const sqlQuery = 'SELECT orderNumber, orderDate FROM orders ORDER BY orderNumber';
  connection.query(sqlQuery, (err, results) => {
    if (err) {
      console.error('Error executing query:', err);
      return;
    }
    // Populate the arrays inside the callback
    for (let i = 0; i < results.length; i++) {
      orderNumberArray[i] = results[i].orderNumber;
    }
    for(let i = 0; i < orderNumberArray.length; i++){
      let date = results[i].orderDate.toString(); // Convert date to string

      // Check if date is a string before using slice
      if (typeof date === 'string') {
        if(!(orderDateArray.includes(date.slice(4,7) + " " + date.slice(11,15)))) {
          orderDateArray[i] = date.slice(4,7) + " " + date.slice(11,15);
        }
      }
      else {
        // Handle the case where date is not a string (log an error, set a default value, etc.)
        console.error('Invalid date format:', date);
      }
    }
    // Now the arrays are populated
    console.log(orderDateArray, orderNumberArray);
  });
}