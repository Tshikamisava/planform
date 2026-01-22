# Project Manager Dashboard

## Overview
The Project Manager Dashboard is a specialized interface for decision makers (DOMs) and administrators to efficiently manage DCR approvals and task assignments.

## Access
The dashboard is accessible to users with the `approve_dcr` permission (typically DOMs and Admins).

**URL:** `/dcr/manager-dashboard`

**Navigation:** A "Manager Dashboard" link appears in the top navigation bar for authorized users.

## Features

### 1. KPI Cards
Four key performance indicators displayed at the top:
- **Pending Approval**: Number of DCRs awaiting approval
- **Approved**: Total approved DCRs
- **Rejected**: Total rejected DCRs
- **Needs Assignment**: Approved DCRs without assigned engineers

### 2. Pending Approvals Section
Displays DCRs that require approval with:
- DCR ID and title
- Priority badge (High/Medium/Low)
- Author name
- Due date
- Estimated cost (if available)
- Quick action buttons:
  - **Approve**: Instantly approve the DCR
  - **Reject**: Opens modal to provide rejection reason
  - **View**: Navigate to detailed DCR view

**Features:**
- Paginated list (10 items per page)
- Color-coded priority badges
- One-click approve with confirmation
- Rejection requires a reason

### 3. Task Assignment Section
Shows approved DCRs that need engineer assignment:
- DCR ID and title
- Approved status badge
- Author information
- Engineer selection dropdown (shows name and department)
- **Assign** button to assign selected engineer

**Features:**
- Paginated list (10 items per page)
- Dropdown lists all active engineers with recipient role
- AJAX-based assignment (no page reload)
- Status automatically changes to "In_Progress" upon assignment
- Creates audit log entry for assignment

## User Actions

### Approve a DCR
1. Click the green **Approve** button on any pending DCR
2. Confirm the approval in the popup dialog
3. DCR status changes to "Approved"
4. Page refreshes to show updated status

### Reject a DCR
1. Click the red **Reject** button on any pending DCR
2. Enter a rejection reason in the modal (required)
3. Click **Confirm Rejection**
4. DCR status changes to "Rejected"
5. Rejection reason is recorded for reference

### Assign an Engineer
1. Locate the approved DCR in the "Needs Engineer Assignment" section
2. Select an engineer from the dropdown menu
3. Click the blue **Assign** button
4. Confirm the assignment
5. DCR status changes to "In_Progress"
6. Engineer is notified of the assignment

### View DCR Details
- Click the **View** button or the DCR ID link to open the full DCR details page

## Technical Details

### Routes
- **Dashboard**: `GET /dcr/manager-dashboard` → `DcrController@managerDashboard`
- **Assign Engineer**: `POST /dcr/{dcr}/assign-engineer` → `DcrController@assignEngineer`

### Permissions Required
- `approve_dcr`: Access dashboard and approve/reject DCRs
- System checks `isDecisionMaker()` or `isAdministrator()` roles

### Data Filtering
- **Non-Admin Users**: See only DCRs assigned to them or unassigned
- **Admins**: See all DCRs across the system

### Audit Logging
All actions are logged:
- Approvals
- Rejections (with reason)
- Engineer assignments

## Benefits

1. **Centralized Management**: All approval tasks in one place
2. **Quick Actions**: Approve/reject without navigating to detail pages
3. **Efficient Assignment**: Assign engineers directly from dashboard
4. **Visual Feedback**: Color-coded priorities and status badges
5. **Comprehensive KPIs**: At-a-glance view of workload
6. **Mobile Responsive**: Works on all device sizes

## Workflow

```
1. Author creates DCR → Status: Pending
2. Manager views in "Pending Approvals"
3. Manager approves → Status: Approved
4. DCR appears in "Needs Assignment" section
5. Manager assigns engineer → Status: In_Progress
6. Engineer completes work → Status: Completed
```

## Notes

- Rejections require a reason to ensure proper documentation
- Only active engineers appear in the assignment dropdown
- Pagination keeps the interface clean with large datasets
- All actions have confirmation dialogs to prevent accidental clicks
- AJAX assignment provides instant feedback without page reload
