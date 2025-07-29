# Extended Database Design - Team Structure & KPI System

## 🏗️ New Database Schema

### 1. Teams Table
```sql
CREATE TABLE teams (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    manager_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (manager_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_manager_id (manager_id)
);
```

### 2. Team Members Table
```sql
CREATE TABLE team_members (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    team_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    role_in_team VARCHAR(100) DEFAULT 'member', -- member, lead, etc.
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_team_user (team_id, user_id),
    INDEX idx_team_id (team_id),
    INDEX idx_user_id (user_id)
);
```

### 3. Updated Tasks Table
```sql
ALTER TABLE tasks ADD COLUMN team_id BIGINT UNSIGNED NULL AFTER user_id;
ALTER TABLE tasks ADD COLUMN assigned_by BIGINT UNSIGNED NULL AFTER team_id;
ALTER TABLE tasks ADD FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE SET NULL;
ALTER TABLE tasks ADD FOREIGN KEY (assigned_by) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE tasks ADD INDEX idx_team_id (team_id);
ALTER TABLE tasks ADD INDEX idx_assigned_by (assigned_by);
```

### 4. KPI Metrics Table
```sql
CREATE TABLE kpi_metrics (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    description TEXT,
    metric_type ENUM('task_completion', 'quality_score', 'deadline_adherence', 'collaboration', 'custom') NOT NULL,
    weight DECIMAL(5,2) DEFAULT 1.00, -- Weight in overall KPI calculation
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL
);
```

### 5. KPI Targets Table
```sql
CREATE TABLE kpi_targets (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    team_id BIGINT UNSIGNED NOT NULL,
    metric_id BIGINT UNSIGNED NOT NULL,
    year YEAR NOT NULL,
    target_value DECIMAL(10,2) NOT NULL,
    current_value DECIMAL(10,2) DEFAULT 0.00,
    set_by BIGINT UNSIGNED NOT NULL, -- Manager who set the target
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (metric_id) REFERENCES kpi_metrics(id) ON DELETE CASCADE,
    FOREIGN KEY (set_by) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_metric_year (user_id, metric_id, year),
    INDEX idx_user_year (user_id, year),
    INDEX idx_team_year (team_id, year)
);
```

### 6. KPI Evaluations Table
```sql
CREATE TABLE kpi_evaluations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    team_id BIGINT UNSIGNED NOT NULL,
    year YEAR NOT NULL,
    quarter TINYINT UNSIGNED NULL, -- 1,2,3,4 for quarterly reviews, NULL for annual
    overall_score DECIMAL(5,2) NOT NULL,
    manager_comments TEXT,
    self_assessment TEXT,
    improvement_areas TEXT,
    achievements TEXT,
    evaluated_by BIGINT UNSIGNED NOT NULL,
    evaluation_date DATE NOT NULL,
    status ENUM('draft', 'submitted', 'approved', 'rejected') DEFAULT 'draft',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
    FOREIGN KEY (evaluated_by) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_year (user_id, year),
    INDEX idx_team_year (team_id, year),
    INDEX idx_evaluation_date (evaluation_date)
);
```

### 7. Task Performance Tracking
```sql
CREATE TABLE task_performance (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    completion_time_hours DECIMAL(8,2),
    quality_score DECIMAL(3,2), -- 0.00 to 5.00
    on_time BOOLEAN DEFAULT FALSE,
    days_overdue INT DEFAULT 0,
    manager_rating DECIMAL(3,2), -- Manager's rating 0.00 to 5.00
    self_rating DECIMAL(3,2), -- User's self rating
    feedback TEXT,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_task_id (task_id),
    INDEX idx_user_id (user_id)
);
```

## 🔄 Updated User Roles

### Role Hierarchy:
1. **Admin**: System administrator, manages all teams and managers
2. **Manager**: Team leader, manages specific team and assigns tasks
3. **User**: Team member, completes assigned tasks

### Updated Users Table:
```sql
ALTER TABLE users ADD COLUMN employee_id VARCHAR(50) UNIQUE NULL AFTER email;
ALTER TABLE users ADD COLUMN hire_date DATE NULL AFTER employee_id;
ALTER TABLE users ADD COLUMN department VARCHAR(100) NULL AFTER hire_date;
ALTER TABLE users ADD COLUMN position VARCHAR(100) NULL AFTER department;
```

## 📊 KPI Calculation Logic

### Automatic Metrics:
1. **Task Completion Rate**: (Completed Tasks / Total Assigned Tasks) * 100
2. **Deadline Adherence**: (On-time Tasks / Total Completed Tasks) * 100
3. **Average Quality Score**: Average of all task quality scores
4. **Collaboration Score**: Based on team interactions and feedback

### Manual Metrics:
1. **Manager Rating**: Direct rating from manager
2. **Peer Review**: Rating from team members
3. **Self Assessment**: User's self-evaluation

## 🚀 Implementation Progress

### ✅ Completed:
1. **Database Schema Design**
   - Teams table with manager relationship
   - Team members with roles and status
   - Updated tasks table for team assignments
   - KPI metrics, targets, evaluations tables
   - Task performance tracking
   - Updated users table with employee info

2. **Laravel Models Created**
   - Team, TeamMember models with relationships
   - KpiMetric, KpiTarget, KpiEvaluation models
   - TaskPerformance model
   - Updated User and Task models with team relationships

3. **Database Migrations**
   - All migration files created for new tables
   - Foreign key relationships established
   - Indexes added for performance

4. **Seeders**
   - KPI metrics seeder with default metrics
   - Teams and members seeder with sample data

5. **API Controllers**
   - TeamController with CRUD operations
   - Permission-based access control

### 🔄 In Progress:
1. **Backend API Development**
   - KPI management endpoints
   - Task assignment with team context
   - Performance tracking APIs

### 📋 Next Steps:
1. **Complete Backend APIs**
   - KpiController for metrics management
   - TaskController updates for team assignments
   - PerformanceController for tracking
   - DashboardController for KPI analytics

2. **Frontend Updates**
   - Admin: Team management interface
   - Manager: Team dashboard, task assignment, KPI setting
   - User: Team view, KPI tracking, performance view

3. **KPI Dashboard Implementation**
   - Individual KPI dashboard
   - Team performance overview
   - Company-wide analytics
   - Annual evaluation system

4. **Testing & Deployment**
   - Unit tests for new features
   - Integration testing
   - Database migration and seeding
   - Documentation updates

## 📊 New System Architecture

```
Company Level
├── Admin
│   ├── Manage all teams
│   ├── Create/delete teams
│   ├── Assign managers
│   └── View company-wide KPIs
│
├── Manager A (Team Alpha)
│   ├── Manage team members
│   ├── Assign tasks to team
│   ├── Set KPI targets
│   ├── Conduct evaluations
│   └── View team performance
│
├── Manager B (Team Beta)
│   └── [Same as Manager A]
│
└── Users (Team Members)
    ├── View assigned tasks
    ├── Track personal KPIs
    ├── Submit self-assessments
    └── View team performance
```

## 🎯 KPI System Features

### For Managers:
- Set annual KPI targets for team members
- Track progress throughout the year
- Conduct quarterly and annual evaluations
- View team performance analytics
- Compare team members' performance

### For Users:
- View personal KPI targets and progress
- Submit self-assessments
- Track task performance metrics
- View historical performance data
- Compare with team averages

### For Admins:
- Configure KPI metrics and weights
- View company-wide performance
- Generate annual reports
- Manage evaluation cycles
- Export performance data
