<?php

function prompt($prompt_msg) {
    echo $prompt_msg . ": ";
    $handle = fopen("php://stdin", "r");
    $line = fgets($handle);
    return trim($line);
}

function createDirectories() {
    $directories = [
        "../pages",
        "../js",
        "../actions",
        "../css",
        "../images",
        "../includes"
    ];
    
    foreach ($directories as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}

// // Database credentials - you can prompt for these or hardcode for testing
// $servername = prompt("Enter the database host");
// $username = prompt("Enter the database username");
// $password = prompt("Enter the database password");
// $dbname = prompt("Enter the database name");

// // Create connection
// $conn = new mysqli($servername, $username, $password);

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }

// // Create database if it doesn't exist
// $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
// if ($conn->query($sql) === TRUE) {
//     echo "Database created successfully or already exists\n";
// } else {
//     die("Error creating database: " . $conn->error);
// }

// // Select the database
// $conn->select_db($dbname);

// // Set charset
// $conn->set_charset("utf8mb4");

// // Define the SQL for creating tables related to faculty module
// $sql = "
// -- Attachments Table
// CREATE TABLE IF NOT EXISTS attachments (
//     attachment_id INT PRIMARY KEY AUTO_INCREMENT,
//     file_path VARCHAR(255) NOT NULL,
//     attachment_type ENUM('attachment', 'gallery_image') NOT NULL,
//     visibility ENUM('show', 'hide') DEFAULT 'show',
//     uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// );

// -- Faculty Table
// CREATE TABLE IF NOT EXISTS faculty (
//     faculty_id INT PRIMARY KEY AUTO_INCREMENT,
//     regdno VARCHAR(20) UNIQUE NOT NULL,
//     first_name VARCHAR(50) NOT NULL,
//     last_name VARCHAR(50),
//     gender ENUM('Male', 'Female', 'Other'),
//     dob DATE,
//     contact_no VARCHAR(15),
//     email VARCHAR(100) UNIQUE NOT NULL,
//     address TEXT,
//     join_date DATE NOT NULL,
//     is_active BOOLEAN DEFAULT TRUE,
//     edit_enabled BOOLEAN DEFAULT TRUE,
//     aadhar_attachment_id INT,
//     pan_attachment_id INT,
//     photo_attachment_id INT,
//     visibility ENUM('show', 'hide') DEFAULT 'show',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (aadhar_attachment_id) REFERENCES attachments(attachment_id) ON DELETE SET NULL,
//     FOREIGN KEY (pan_attachment_id) REFERENCES attachments(attachment_id) ON DELETE SET NULL,
//     FOREIGN KEY (photo_attachment_id) REFERENCES attachments(attachment_id) ON DELETE SET NULL
// );

// -- Faculty Additional Details Table
// CREATE TABLE IF NOT EXISTS faculty_additional_details (
//     detail_id INT PRIMARY KEY AUTO_INCREMENT,
//     faculty_id INT NOT NULL,
//     department VARCHAR(255),
//     position VARCHAR(255),
//     profilepic VARCHAR(255),
//     father_name VARCHAR(255),
//     father_occupation VARCHAR(255),
//     mother_name VARCHAR(255),
//     mother_occupation VARCHAR(255),
//     marital_status VARCHAR(20),
//     spouse_name VARCHAR(255),
//     spouse_occupation VARCHAR(255),
//     nationality VARCHAR(255),
//     religion VARCHAR(255),
//     category VARCHAR(255),
//     caste VARCHAR(255),
//     sub_caste VARCHAR(255),
//     aadhar_no VARCHAR(20),
//     pan_no VARCHAR(20),
//     contact_no2 VARCHAR(20),
//     blood_group VARCHAR(10),
//     permanent_address TEXT,
//     correspondence_address TEXT,
//     scopus_author_id VARCHAR(255),
//     orcid_id VARCHAR(255),
//     google_scholar_id_link VARCHAR(255),
//     aicte_id VARCHAR(255),
//     scet_id VARCHAR(255),
//     visibility ENUM('show', 'hide') DEFAULT 'show',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE CASCADE
// );

// -- Work Experiences Table
// CREATE TABLE IF NOT EXISTS work_experiences (
//     experience_id INT PRIMARY KEY AUTO_INCREMENT,
//     faculty_id INT NOT NULL,
//     institution_name VARCHAR(255) NOT NULL,
//     experience_type ENUM('Teaching', 'Industry') NOT NULL,
//     designation VARCHAR(255),
//     from_date DATE,
//     to_date DATE,
//     number_of_years INT,
//     responsibilities TEXT,
//     service_certificate_attachment_id INT,
//     visibility ENUM('show', 'hide') DEFAULT 'show',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE CASCADE,
//     FOREIGN KEY (service_certificate_attachment_id) REFERENCES attachments(attachment_id) ON DELETE SET NULL
// );

// -- Common Lookup Tables (Normalized)
// CREATE TABLE IF NOT EXISTS lookup_tables (
//     lookup_id INT PRIMARY KEY AUTO_INCREMENT,
//     lookup_type VARCHAR(50) NOT NULL,
//     lookup_value VARCHAR(100) NOT NULL,
//     UNIQUE KEY unique_lookup (lookup_type, lookup_value)
// );

// -- Teaching Activities Table
// CREATE TABLE IF NOT EXISTS teaching_activities (
//     activity_id INT PRIMARY KEY AUTO_INCREMENT,
//     faculty_id INT NOT NULL,
//     course_name VARCHAR(200) NOT NULL,
//     semester VARCHAR(20),
//     year YEAR,
//     course_code VARCHAR(20),
//     description TEXT,
//     attachment_id INT,
//     visibility ENUM('show', 'hide') DEFAULT 'show',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE CASCADE,
//     FOREIGN KEY (attachment_id) REFERENCES attachments(attachment_id) ON DELETE SET NULL
// );

// -- Research Publications Table
// CREATE TABLE IF NOT EXISTS research_publications (
//     publication_id INT PRIMARY KEY AUTO_INCREMENT,
//     faculty_id INT NOT NULL,
//     title VARCHAR(200) NOT NULL,
//     journal_name VARCHAR(200),
//     type_id INT,
//     publication_date DATE,
//     doi VARCHAR(50),
//     description TEXT,
//     attachment_id INT,
//     visibility ENUM('show', 'hide') DEFAULT 'show',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE CASCADE,
//     FOREIGN KEY (type_id) REFERENCES lookup_tables(lookup_id) ON DELETE SET NULL,
//     FOREIGN KEY (attachment_id) REFERENCES attachments(attachment_id) ON DELETE SET NULL
// );

// -- Workshops and Seminars Table
// CREATE TABLE IF NOT EXISTS workshops_seminars (
//     workshop_id INT PRIMARY KEY AUTO_INCREMENT,
//     faculty_id INT NOT NULL,
//     title VARCHAR(200) NOT NULL,
//     type_id INT,
//     location VARCHAR(100),
//     organized_by VARCHAR(200),
//     date DATE,
//     description TEXT,
//     attachment_id INT,
//     visibility ENUM('show', 'hide') DEFAULT 'show',
//     created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     FOREIGN KEY (faculty_id) REFERENCES faculty(faculty_id) ON DELETE CASCADE,
//     FOREIGN KEY (type_id) REFERENCES lookup_tables(lookup_id) ON DELETE SET NULL,
//     FOREIGN KEY (attachment_id) REFERENCES attachments(attachment_id) ON DELETE SET NULL
// );

// ";

// // Execute multi-query
// if ($conn->multi_query($sql) === TRUE) {
//     echo "Faculty tables created successfully\n";
// } else {
//     echo "Error creating faculty tables: " . $conn->error . "\n";
// }

// // Wait for multi_query to finish
// while ($conn->next_result()) {
//     if ($result = $conn->store_result()) {
//         $result->free();
//     }
// }

// // Insert lookup values
// $lookup_values = "
// -- Insert faculty-related lookup values
// INSERT INTO lookup_tables (lookup_type, lookup_value) VALUES 
// ('publication_type', 'Journal'), 
// ('publication_type', 'Conference'),
// ('publication_type', 'Book'),
// ('publication_type', 'Book Chapter'),
// ('workshop_type', 'Workshop'),
// ('workshop_type', 'Seminar'),
// ('workshop_type', 'Conference'),
// ('workshop_type', 'Training')
// ";

// if ($conn->multi_query($lookup_values) === TRUE) {
//     echo "Lookup values inserted successfully\n";
// } else {
//     echo "Error inserting lookup values: " . $conn->error . "\n";
// }

// // Wait for multi_query to finish
// while ($conn->next_result()) {
//     if ($result = $conn->store_result()) {
//         $result->free();
//     }
// }

// Create necessary directories
createDirectories();

// Include CRUDGenerator (make sure this file exists)
require_once "generator/CRUDGeneratorv9.php";

// Define tables and their columns for the CRUD generator
$tables = [
    'faculty' => ['faculty_id', 'regdno', 'first_name', 'last_name', 'gender', 'dob', 'contact_no', 'email', 'address', 'join_date', 'is_active', 'edit_enabled', 'visibility'],
    'faculty_additional_details' => ['detail_id', 'faculty_id', 'department', 'position', 'blood_group', 'nationality', 'religion', 'category', 'aadhar_no', 'pan_no', 'visibility'],
    'work_experiences' => ['experience_id', 'faculty_id', 'institution_name', 'experience_type', 'designation', 'from_date', 'to_date', 'number_of_years', 'visibility'],
    'teaching_activities' => ['activity_id', 'faculty_id', 'course_name', 'semester', 'year', 'course_code', 'visibility'],
    'research_publications' => ['publication_id', 'faculty_id', 'title', 'journal_name', 'type_id', 'publication_date', 'doi', 'visibility'],
    'workshops_seminars' => ['workshop_id', 'faculty_id', 'title', 'type_id', 'location', 'organized_by', 'date', 'visibility'],
    'attachments' => ['attachment_id', 'file_path', 'attachment_type', 'visibility'],
    'lookup_tables' => ['lookup_id', 'lookup_type', 'lookup_value']
];

// Define foreign key relationships
$foreignKeys = [
    'faculty_additional_details' => [
        'faculty_id' => ['table' => 'faculty', 'key' => 'faculty_id', 'field' => 'first_name']
    ],
    'work_experiences' => [
        'faculty_id' => ['table' => 'faculty', 'key' => 'faculty_id', 'field' => 'first_name'],
        'service_certificate_attachment_id' => ['table' => 'attachments', 'key' => 'attachment_id', 'field' => 'file_path']
    ],
    'teaching_activities' => [
        'faculty_id' => ['table' => 'faculty', 'key' => 'faculty_id', 'field' => 'first_name'],
        'attachment_id' => ['table' => 'attachments', 'key' => 'attachment_id', 'field' => 'file_path']
    ],
    'research_publications' => [
        'faculty_id' => ['table' => 'faculty', 'key' => 'faculty_id', 'field' => 'first_name'],
        'type_id' => ['table' => 'lookup_tables', 'key' => 'lookup_id', 'field' => 'lookup_value'],
        'attachment_id' => ['table' => 'attachments', 'key' => 'attachment_id', 'field' => 'file_path']
    ],
    'workshops_seminars' => [
        'faculty_id' => ['table' => 'faculty', 'key' => 'faculty_id', 'field' => 'first_name'],
        'type_id' => ['table' => 'lookup_tables', 'key' => 'lookup_id', 'field' => 'lookup_value'],
        'attachment_id' => ['table' => 'attachments', 'key' => 'attachment_id', 'field' => 'file_path']
    ],
    'faculty' => [
        'aadhar_attachment_id' => ['table' => 'attachments', 'key' => 'attachment_id', 'field' => 'file_path'],
        'pan_attachment_id' => ['table' => 'attachments', 'key' => 'attachment_id', 'field' => 'file_path'],
        'photo_attachment_id' => ['table' => 'attachments', 'key' => 'attachment_id', 'field' => 'file_path']
    ]
];

// Generate CRUD files for each table
foreach ($tables as $table => $columns) {
    $generator = new CRUDGenerator($table, $columns, $foreignKeys[$table] ?? []);
    $generator->generateFiles();
    echo "Generated CRUD files for $table\n";
}

echo "CRUD files for faculty module generated successfully.\n";

$conn->close();
?>