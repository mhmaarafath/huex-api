## Installation

- create database
- clone repository
- rename .env.exampe as .env
- add database credentials
- run "php artisan migrate:fresh --seed"
- run "php artisan storage:link"
- default admin user (email: admin@huex.com, password : password)

## You check api.php file's api from postman

## Sanctum token based authentication has implemented

1.  - 'register' - 3 types of roles accepted validation based on role type. Common user details save in users table. Separate tables for students and teaches
2.  - 'register' - for all types of user signups
3.  - 'login'
4.  - 'modules' - Modules are combination of subjects and grades. Optional middleware has used. Response will differance based on request bearer token availability
5.  - 'subjects' - Resource api. Admin middleware
    - 'users/{user}/toggle-user-state' - Toggle user's active inactive status
6.  - 'students/subjects' - View current subjects of student
    - 'assignments' - Will return students assignments, answers with marks and rank
    - 'students/subjects/{subject}/favourite' - Favourite a subject
    - 'update-profile' - Name, email, password update common for all system users
    - 'students/subjects/{subject}' - Delete method for detach and patch method for attach subjects
    - 'answers' - Student can upload answers, remove, update answer files
7.  - 'teachers/students' - Students based on teacher's subject and grade
    - 'answers/{answer}/marks' - Teacher can update marks for his/her added assignments
    - 'assignments' - Upload assignments
    - 'assignments/{assignment}/ranks' - This request will update ranks of students per assignment


## Areas to improve 

- Use gates and policies 
- Test cases
   
