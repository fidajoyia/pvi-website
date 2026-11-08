# Prime Vocational Institute — WordPress theme

WordPress version of the static site in `../college-website`. Same design, but all data
lives in the WordPress database and is managed from wp-admin:

| wp-admin menu | What it does |
|---|---|
| **Applications** | Online admission forms submitted on the website. Change status (Pending / Accepted / Rejected), search by name / CNIC / phone, export to Excel (CSV). New applications are also emailed to the office. |
| **Notifications** | Announcements shown on the home page ticker and Notifications page. Tick *Important* to highlight. |
| **Results** | Result cards. Students look them up by roll number on the Results page. |
| **Courses** | Course list, grouped by *Categories*. Shown on the Courses page and in the Apply form. |
| **Institute Settings** | Name, phones, address, home page text, NAVTTC info, posters. (Administrators only.) |

## Accounts

- **Administrator** — full control (the "super admin").
- **Institute Staff** — can manage applications, notifications, results and courses only.
  Add staff in *Users > Add New* and choose the role *Institute Staff*.

## Install

1. Zip the `pvi` folder (or use `pvi-theme.zip`).
2. wp-admin > Appearance > Themes > Add New > Upload Theme > choose the zip > Install > Activate.
3. On first activation the theme creates the pages (Home, Courses, Apply Online, Notifications,
   Results, Contact), sets Home as the front page, and imports the original courses and notifications.

Works with Hostinger's LiteSpeed Cache: the cache is purged automatically when content changes.
