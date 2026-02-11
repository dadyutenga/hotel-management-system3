# Laundry Module Documentation

## Overview
The Laundry Module is a comprehensive system for managing guest laundry services within the Hotel Management System. It enables supervisors to assign laundry tasks to house help staff, track the status of laundry items, and manage billing (amenity vs paid services).

---

## Access Levels & Permissions

### 1. **Administrator**
- **Email:** `admin@hotel.com`
- **Password:** `password`
- **Permissions:**
  - Full CRUD operations (Create, Read, Update, Delete)
  - View all laundry tasks across the system
  - Assign tasks to any house help staff
  - Change task status at any stage
  - Delete any task
  - Access comprehensive dashboard with laundry statistics

### 2. **Supervisor**
- **Email:** `supervisor@hotel.com`
- **Password:** `password`
- **Permissions:**
  - Full CRUD operations (Create, Read, Update, Delete)
  - View all laundry tasks
  - Assign tasks to house help staff
  - Change task status (pending → in progress → completed → returned)
  - Delete any task
  - Access supervisor dashboard with laundry statistics
  - Monitor house help performance

### 3. **House Help**
- **Email:** `househelp@hotel.com`
- **Password:** `password`
- **Permissions:**
  - View only tasks assigned to them
  - Mark completed tasks as "Returned" (delivered back to guest)
  - Access personal dashboard showing their task statistics
  - **Cannot:** Create, edit, or delete tasks
  - **Cannot:** View other house help's tasks

### 4. **Front Desk**
- **Email:** `frontdesk@hotel.com`
- **Password:** `password`
- **Permissions:**
  - No access to laundry module (can be added if business requirements change)

---

## Laundry Workflow

```
[Supervisor Creates Task] 
        ↓
[Assigns to House Help]
        ↓
[Status: Pending]
        ↓
[House Help Collects Items]
        ↓
[Status: In Progress]
        ↓
[Laundry Completed]
        ↓
[Status: Completed]
        ↓
[House Help Returns to Guest]
        ↓
[Status: Returned]
```

---

## Task Status Types

| Status | Description | Who Can Set |
|--------|-------------|-------------|
| **Pending** | Task created, awaiting collection | Supervisor/Admin (on creation) |
| **In Progress** | Items collected, laundry in process | Supervisor/Admin |
| **Completed** | Laundry finished, ready for return | Supervisor/Admin |
| **Returned** | Items delivered back to guest | Supervisor/Admin/House Help |

---

## Billing Types

### 1. **Amenity (Free Service)**
- Complimentary laundry service for guests
- Cost: $0.00
- Not added to guest invoice
- Common for VIP guests or promotional packages

### 2. **Paid Service**
- Billable laundry service
- Custom cost per task
- **Will be added to guest invoice** (future invoice module integration)
- Tracked separately for accounting purposes

---

## Key Features

### For Supervisors/Admins:
- ✅ Create laundry tasks linked to active reservations
- ✅ Assign tasks to specific house help staff
- ✅ Describe items (e.g., "2 shirts, 1 pants, 3 towels")
- ✅ Set service as amenity or paid
- ✅ Track all tasks with real-time status updates
- ✅ Dashboard statistics showing:
  - Pending tasks
  - Tasks in progress
  - Completed tasks
  - Today's laundry count

### For House Help:
- ✅ Personal dashboard showing assigned tasks
- ✅ View task details (guest name, room number, items)
- ✅ Mark completed tasks as returned
- ✅ Performance metrics (completion rate, active tasks)

---

## Navigation Structure

### Admin/Supervisor Access:
```
Dashboard → Operations → Laundry
```
**Available Actions:**
- View All Tasks
- Create New Task
- Edit Task
- Delete Task
- Change Status
- View Statistics

### House Help Access:
```
Dashboard → My Tasks → Laundry Tasks
```
**Available Actions:**
- View My Assigned Tasks
- Mark as Returned (when completed)

---

## Technical Implementation

### Database Table: `laundry_tasks`
- **Primary Key:** UUID
- **Key Fields:**
  - `task_number` - Unique identifier (e.g., LND-ABC123)
  - `reservation_id` - Linked to guest reservation
  - `assigned_to` - House help user ID
  - `created_by` - Supervisor/admin who created task
  - `guest_name` - Denormalized for quick access
  - `room_number` - Denormalized for quick access
  - `status` - Current task status
  - `is_amenity` - Boolean (free vs paid)
  - `cost` - Service cost if paid
  - `collected_at`, `completed_at`, `returned_at` - Workflow timestamps

### Models:
- `LaundryTask` - Main model with relationships to User, Reservation
- Relationships: `belongsTo(User)`, `belongsTo(Reservation)`

### Controllers:
- `LaundryTaskController` - Handles all CRUD operations and status changes
- Role-based method access control

---

## Future Enhancements (Ready for Implementation)

1. **Invoice Integration**
   - Automatically add non-amenity charges to guest invoices
   - Generate itemized billing statements
   - `Reservation::getTotalLaundryChargesAttribute()` already implemented

2. **Notifications**
   - Email/SMS notifications when tasks are assigned
   - Alerts when tasks are completed/returned

3. **Reporting**
   - House help performance reports
   - Revenue from laundry services
   - Most common items laundered

4. **Per-Item Pricing**
   - Configurable pricing for different garment types
   - Automatic cost calculation

---

## Setup Instructions

### Initial Setup:
```bash
# Run migrations
php artisan migrate

# Seed roles and users
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=UserSeeder

# Clear caches
php artisan config:clear
php artisan view:clear
php artisan cache:clear
```

### Testing the Module:
1. Login as Supervisor: `supervisor@hotel.com` / `password`
2. Navigate to: **Operations → Laundry**
3. Click **"New Laundry Task"**
4. Select a reservation with an active checked-in guest
5. Assign to House Help: `househelp@hotel.com`
6. Set as amenity or paid service
7. Submit task
8. Logout and login as House Help
9. View assigned task in **My Tasks**
10. Mark task as returned when completed

---

## Security Features

- ✅ Role-based access control (RBAC)
- ✅ Route middleware protection
- ✅ Controller-level permission checks
- ✅ House help can only access their own tasks
- ✅ Audit trail via timestamps and creator tracking
- ✅ Soft delete capability (can be implemented)

---

## API Endpoints (Routes)

```php
// Accessible by Admin, Supervisor, House Help
GET  /laundry                              // List tasks
POST /laundry/{laundryTask}/mark-returned  // Mark as returned

// Accessible by Admin, Supervisor only
GET    /laundry/create                     // Show create form
POST   /laundry                            // Store new task
GET    /laundry/{laundryTask}/edit         // Show edit form
PUT    /laundry/{laundryTask}              // Update task
DELETE /laundry/{laundryTask}              // Delete task
POST   /laundry/{laundryTask}/mark-in-progress  // Start task
POST   /laundry/{laundryTask}/mark-completed    // Complete task
```

---

## Support & Troubleshooting

### Common Issues:

**403 Unauthorized Error:**
- Verify user role in database
- Check `role_id` matches correct role
- Clear cache: `php artisan cache:clear`

**House Help can't see tasks:**
- Verify task is assigned to their user ID
- Check `assigned_to` field in database

**Can't create task:**
- Ensure reservation exists and is active (confirmed/checked_in)
- Verify room is assigned to reservation
- Check house help users exist in system

---

## Module Summary

The Laundry Module provides a complete workflow for managing guest laundry services with:
- 🎯 Role-based access control
- 📊 Real-time tracking and statistics
- 💰 Billing management (amenity vs paid)
- 🔄 Complete status workflow
- 📱 Responsive UI matching hotel design
- 🚀 Future-ready for invoice integration

**Total Implementation:** 4 user roles, 8 routes, 3 views, complete CRUD with professional UI.