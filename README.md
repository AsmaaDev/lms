

## About project

Simple Library Management System

This project is a Simple Library Management System designed to help manage the essential functions of a library. The system is built using Express and Socket.IO for real-time interactions and uses MySQL as the database to store information.
Features

    User Management: Add, update, and delete users.
    Book Management: Add, update, and delete book information.
    Borrow/Return Tracking: Track borrowed and returned books.
    Real-Time Notifications: Notify users about due dates and other updates using Socket.IO.
    History Tracking: Maintain a history of transactions, including borrowed and returned books, in the order_histories table.

Technologies Used

    Express: For creating a RESTful API.
    Socket.IO: For real-time communication.
    MySQL: To store user data, book data, and transaction histories.

Setup and Installation

   

Install dependencies:



npm install

Configure the MySQL database settings in the .env file.
Run the application:


    npm start

Contribution

Feel free to fork this project and submit pull requests. Your contributions are highly appreciated!
License

This project is licensed under the MIT License.
