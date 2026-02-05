# RRMS Database Structure Reference

**Database:** rrms  
**Total Tables:** 18  
**Generated:** 2026-02-05

---

## Table of Contents

1. [User Management](#user-management)
2. [Location & Zone Management](#location--zone-management)
3. [Room & Bed Management](#room--bed-management)
4. [Booking System](#booking-system)
5. [Meals](#meals)
6. [Complaints](#complaints)
7. [Feedback System](#feedback-system)

---

## User Management

### users

Main user table for system authentication and profile management.

| Column Name     | Type         | Nullable | Key | Default             | Extra                         |
| --------------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id              | int(11)      | NO       | PRI | NULL                | auto_increment                |
| name            | varchar(255) | NO       |     | NULL                |                               |
| username        | varchar(255) | NO       | UNI | NULL                |                               |
| email           | varchar(255) | NO       | UNI | NULL                |                               |
| mobile          | varchar(20)  | YES      |     | NULL                |                               |
| designation     | varchar(255) | YES      |     | NULL                |                               |
| division_id     | int(11)      | YES      | MUL | NULL                |                               |
| zone_id         | int(11)      | YES      | MUL | NULL                |                               |
| location_id     | int(11)      | YES      | MUL | NULL                |                               |
| password        | varchar(255) | NO       |     | NULL                |                               |
| user_type       | int(11)      | YES      | MUL | NULL                |                               |
| department      | varchar(255) | YES      |     | NULL                |                               |
| staff_type      | varchar(255) | YES      |     | NULL                |                               |
| head_quarter    | varchar(255) | YES      |     | NULL                |                               |
| gender          | varchar(50)  | YES      |     | NULL                |                               |
| signup_status   | varchar(50)  | YES      |     | NULL                |                               |
| status          | varchar(50)  | NO       |     | active              |                               |
| expo_token      | varchar(500) | YES      |     | NULL                |                               |
| profile_picture | varchar(500) | YES      |     | NULL                |                               |
| created_at      | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at      | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

### user_types

User role types and permissions.

| Column Name | Type         | Nullable | Key | Default             | Extra                         |
| ----------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id          | int(11)      | NO       | PRI | NULL                | auto_increment                |
| type        | varchar(255) | NO       |     | NULL                |                               |
| role        | varchar(255) | NO       |     | NULL                |                               |
| created_at  | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at  | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

### staffs

Staff member details and documentation.

| Column Name         | Type         | Nullable | Key | Default             | Extra                         |
| ------------------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id                  | int(11)      | NO       | PRI | NULL                | auto_increment                |
| name                | varchar(255) | NO       |     | NULL                |                               |
| designation         | varchar(255) | YES      |     | NULL                |                               |
| contact_no          | varchar(20)  | YES      |     | NULL                |                               |
| location_id         | int(11)      | NO       | MUL | NULL                |                               |
| type                | tinyint(4)   | NO       |     | 0                   |                               |
| id_proof            | varchar(500) | YES      |     | NULL                |                               |
| police_verification | varchar(500) | YES      |     | NULL                |                               |
| medical_certificate | varchar(500) | YES      |     | NULL                |                               |
| created_at          | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at          | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

---

## Location & Zone Management

### locations

Location/facility information with subscription details.

| Column Name        | Type          | Nullable | Key | Default             | Extra                         |
| ------------------ | ------------- | -------- | --- | ------------------- | ----------------------------- |
| id                 | int(11)       | NO       | PRI | NULL                | auto_increment                |
| name               | varchar(255)  | NO       |     | NULL                |                               |
| short_name         | varchar(50)   | NO       |     | NULL                |                               |
| zone_id            | int(11)       | NO       | MUL | NULL                |                               |
| division_id        | int(11)       | NO       | MUL | NULL                |                               |
| is_subscribed      | tinyint(1)    | NO       |     | 1                   |                               |
| longitude          | decimal(11,8) | YES      |     | NULL                |                               |
| latitude           | decimal(10,8) | YES      |     | NULL                |                               |
| subscription_start | datetime      | YES      |     | NULL                |                               |
| subscription_end   | datetime      | YES      |     | NULL                |                               |
| created_at         | timestamp     | NO       |     | current_timestamp() |                               |
| updated_at         | timestamp     | NO       |     | current_timestamp() | on update current_timestamp() |

### zones

Zone groupings for organizing locations.

| Column Name | Type         | Nullable | Key | Default             | Extra                         |
| ----------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id          | int(11)      | NO       | PRI | NULL                | auto_increment                |
| name        | varchar(255) | NO       |     | NULL                |                               |
| created_at  | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at  | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

### divisions

Department divisions within zones.

| Column Name | Type         | Nullable | Key | Default             | Extra                         |
| ----------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id          | int(11)      | NO       | PRI | NULL                | auto_increment                |
| zone_id     | int(11)      | NO       | MUL | NULL                |                               |
| name        | varchar(255) | NO       |     | NULL                |                               |
| created_at  | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at  | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

---

## Room & Bed Management

### rooms

Room information and capacity details.

| Column Name | Type         | Nullable | Key | Default             | Extra                         |
| ----------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id          | int(11)      | NO       | PRI | NULL                | auto_increment                |
| user_id     | int(11)      | NO       | MUL | NULL                |                               |
| room        | varchar(255) | NO       |     | NULL                |                               |
| gender      | varchar(50)  | NO       |     | NULL                |                               |
| no_of_bed   | int(11)      | NO       |     | NULL                |                               |
| location_id | int(11)      | NO       | MUL | NULL                |                               |
| division_id | int(11)      | NO       | MUL | NULL                |                               |
| status      | varchar(50)  | NO       |     | active              |                               |
| created_at  | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at  | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

### beds

Individual bed allocation and status.

| Column Name     | Type         | Nullable | Key | Default             | Extra                         |
| --------------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id              | int(11)      | NO       | PRI | NULL                | auto_increment                |
| checkin_user_id | int(11)      | YES      | MUL | NULL                |                               |
| room_id         | int(11)      | NO       | MUL | NULL                |                               |
| division_id     | int(11)      | NO       | MUL | NULL                |                               |
| location_id     | int(11)      | NO       | MUL | NULL                |                               |
| gender          | varchar(50)  | YES      |     | NULL                |                               |
| status          | tinyint(4)   | NO       |     | 0                   |                               |
| bed_name        | varchar(255) | YES      |     | NULL                |                               |
| created_at      | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at      | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

---

## Booking System

### bookings

Main booking records for room allocations.

| Column Name               | Type         | Nullable | Key | Default             | Extra                         |
| ------------------------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id                        | int(11)      | NO       | PRI | NULL                | auto_increment                |
| user_id                   | int(11)      | NO       | MUL | NULL                |                               |
| booking_req_id            | int(11)      | YES      | MUL | NULL                |                               |
| division_id               | int(11)      | NO       | MUL | NULL                |                               |
| room_id                   | int(11)      | NO       | MUL | NULL                |                               |
| bed_id                    | int(11)      | NO       | MUL | NULL                |                               |
| location_id               | int(11)      | NO       | MUL | NULL                |                               |
| request_checkout_datetime | datetime     | YES      |     | NULL                |                               |
| actual_checkin_datetime   | datetime     | YES      |     | NULL                |                               |
| checkout_datetime         | varchar(255) | YES      |     | NULL                |                               |
| wakeup_time               | datetime     | YES      |     | NULL                |                               |
| wakeup_status             | tinyint(4)   | YES      |     | NULL                |                               |
| booking_status            | tinyint(4)   | NO       |     | 0                   |                               |
| created_at                | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at                | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

### booking_request

Booking requests with train and availability information.

| Column Name               | Type                         | Nullable | Key | Default             | Extra                         |
| ------------------------- | ---------------------------- | -------- | --- | ------------------- | ----------------------------- |
| id                        | int(11)                      | NO       | PRI | NULL                | auto_increment                |
| user_id                   | int(11)                      | NO       | MUL | NULL                |                               |
| arriving_train            | varchar(255)                 | YES      |     | NULL                |                               |
| departure_train           | varchar(255)                 | YES      |     | NULL                |                               |
| booking_location          | int(11)                      | YES      | MUL | NULL                |                               |
| booking_request_datetime  | datetime                     | YES      |     | NULL                |                               |
| request_checkin_datetime  | datetime                     | YES      |     | NULL                |                               |
| request_checkout_datetime | datetime                     | YES      |     | NULL                |                               |
| off_duty_datetime         | datetime                     | YES      |     | NULL                |                               |
| status                    | tinyint(4)                   | NO       |     | 0                   |                               |
| booking_by                | enum('Lobby','App','Webapp') | YES      |     | NULL                |                               |
| created_at                | timestamp                    | NO       |     | current_timestamp() |                               |
| updated_at                | timestamp                    | NO       |     | current_timestamp() | on update current_timestamp() |

---

## Meals

### meal_types

Available meal type definitions with pricing.

| Column Name | Type          | Nullable | Key | Default             | Extra                         |
| ----------- | ------------- | -------- | --- | ------------------- | ----------------------------- |
| id          | int(11)       | NO       | PRI | NULL                | auto_increment                |
| meal_type   | varchar(100)  | NO       |     | NULL                |                               |
| price       | decimal(10,2) | NO       |     | NULL                |                               |
| location_id | int(11)       | NO       | MUL | NULL                |                               |
| created_at  | timestamp     | NO       |     | current_timestamp() |                               |
| updated_at  | timestamp     | NO       |     | current_timestamp() | on update current_timestamp() |

### meals

Meal orders with payment tracking.

| Column Name    | Type                       | Nullable | Key | Default             | Extra                         |
| -------------- | -------------------------- | -------- | --- | ------------------- | ----------------------------- |
| id             | int(11)                    | NO       | PRI | NULL                | auto_increment                |
| meal_type      | varchar(255)               | YES      |     | NULL                |                               |
| user_id        | int(11)                    | NO       | MUL | NULL                |                               |
| booking_id     | int(11)                    | NO       | MUL | NULL                |                               |
| room_id        | int(11)                    | NO       | MUL | NULL                |                               |
| bed_id         | int(11)                    | NO       | MUL | NULL                |                               |
| payment_mode   | enum('Cash','Online','NA') | YES      |     | NULL                |                               |
| payment_status | tinyint(4)                 | NO       |     | 0                   |                               |
| payment_code   | varchar(255)               | YES      |     | NULL                |                               |
| transaction_id | varchar(255)               | YES      |     | NULL                |                               |
| location_id    | int(11)                    | NO       | MUL | NULL                |                               |
| created_at     | timestamp                  | NO       |     | current_timestamp() |                               |
| updated_at     | timestamp                  | NO       |     | current_timestamp() | on update current_timestamp() |

---

## Complaints

### complaints

Main complaint records with details.

| Column Name    | Type         | Nullable | Key | Default             | Extra                         |
| -------------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id             | int(11)      | NO       | PRI | NULL                | auto_increment                |
| user_id        | int(11)      | NO       | MUL | NULL                |                               |
| booking_id     | int(11)      | NO       | MUL | NULL                |                               |
| location_id    | int(11)      | NO       | MUL | NULL                |                               |
| room_no        | varchar(255) | YES      |     | NULL                |                               |
| bed_no         | varchar(255) | YES      |     | NULL                |                               |
| complaint_type | varchar(255) | YES      |     | NULL                |                               |
| remarks        | text         | YES      |     | NULL                |                               |
| photo          | varchar(500) | YES      |     | NULL                |                               |
| status         | varchar(50)  | YES      |     | NULL                |                               |
| created_at     | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at     | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

### complaint_types

Complaint type categories by location.

| Column Name    | Type         | Nullable | Key | Default             | Extra                         |
| -------------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id             | int(11)      | NO       | PRI | NULL                | auto_increment                |
| location_id    | int(11)      | NO       | MUL | NULL                |                               |
| complaint_type | varchar(100) | NO       |     | NULL                |                               |
| created_at     | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at     | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

### complaint_process

Complaint workflow and status tracking.

| Column Name  | Type         | Nullable | Key | Default             | Extra                         |
| ------------ | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id           | int(11)      | NO       | PRI | NULL                | auto_increment                |
| user_id      | int(11)      | NO       | MUL | NULL                |                               |
| complaint_id | int(11)      | NO       | MUL | NULL                |                               |
| remark       | text         | YES      |     | NULL                |                               |
| status       | varchar(50)  | YES      |     | NULL                |                               |
| attachment   | varchar(500) | YES      |     | NULL                |                               |
| created_at   | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at   | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

---

## Feedback System

### feedback_parameters

Feedback rating parameters.

| Column Name | Type         | Nullable | Key | Default             | Extra                         |
| ----------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id          | int(11)      | NO       | PRI | NULL                | auto_increment                |
| name        | varchar(255) | NO       |     | NULL                |                               |
| location_id | int(11)      | NO       | MUL | NULL                |                               |
| created_at  | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at  | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

### feedback_values

Staff feedback records with ratings.

| Column Name       | Type         | Nullable | Key | Default             | Extra                         |
| ----------------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id                | int(11)      | NO       | PRI | NULL                | auto_increment                |
| staff_id          | varchar(255) | NO       |     | NULL                |                               |
| user_id           | int(11)      | NO       | MUL | NULL                |                               |
| location_id       | int(11)      | NO       | MUL | NULL                |                               |
| feedback_param_id | int(11)      | NO       |     | NULL                |                               |
| value             | int(11)      | YES      |     | NULL                |                               |
| remarks           | text         | YES      |     | NULL                |                               |
| created_at        | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at        | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

### feedbacks

User feedback with comments.

| Column Name | Type         | Nullable | Key | Default             | Extra                         |
| ----------- | ------------ | -------- | --- | ------------------- | ----------------------------- |
| id          | int(11)      | NO       | PRI | NULL                | auto_increment                |
| staff_id    | varchar(255) | NO       |     | NULL                |                               |
| user_id     | int(11)      | NO       | MUL | NULL                |                               |
| location    | int(11)      | NO       | MUL | NULL                |                               |
| comments    | text         | YES      |     | NULL                |                               |
| created_at  | timestamp    | NO       |     | current_timestamp() |                               |
| updated_at  | timestamp    | NO       |     | current_timestamp() | on update current_timestamp() |

---

## Quick Access URLs

**View Database Structure:**

- HTML Format: `http://localhost/RRMS/DB_STRUCTURE_REFERENCE.php`
- JSON Format: `http://localhost/RRMS/DB_STRUCTURE_REFERENCE.php?format=json`
- Specific Table: `http://localhost/RRMS/DB_STRUCTURE_REFERENCE.php?table=tablename`

## Key Relationships

- **users** → user_types (user_type)
- **users** → divisions, zones, locations (foreign keys)
- **bookings** → users, rooms, beds, divisions, locations
- **booking_request** → users, locations
- **complaints** → users, bookings, locations
- **complaint_process** → users, complaints
- **meals** → users, bookings, rooms, beds, locations
- **feedbacks** → users, locations
- **feedback_values** → users, feedback_parameters

---

**Last Updated:** 2026-02-05  
**Auto-generated Reference File**
