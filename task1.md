You are working on a Laravel Blade-based web application.

Task:
Remove the "MRK Hotel Management" branding (text/logo) and  the  cube from the top-left corner of the application layout  and  the  sidebars  .

Context:
- The UI is rendered using Blade templates.
- The branding appears in the main layout file (likely inside resources/views/layouts/ or similar).
- It is part of the sidebar or top navigation area.

Instructions:
1. Locate the main layout file:
   - Check files like:
     - resources/views/layouts/app.blade.php
     - resources/views/layouts/main.blade.php
     - resources/views/components/layout.blade.php
     resources\views\shared\sidebar
     - or any layout used by the dashboard view.

2. Find the section responsible for rendering the top-left branding:
   - Look for:
     - "MRK Hotel Management"
     - <h1>, <span>, <div>, or <a> containing that text
     - logo image or branding container

3. Remove or comment out ONLY the branding element:
   - Do NOT break layout structure
   - Do NOT remove navigation/sidebar functionality
   - Keep spacing/layout intact (adjust CSS classes if needed)

4. If the branding is inside a reusable component:
   - Update the component instead of duplicating changes

5. If styles depend on that element:
   - Clean up unused CSS classes if necessary
   - Ensure UI alignment remains correct

6. After removal:
   - Ensure no empty container causes layout shift
   - Verify dashboard still renders properly

Output:
- Show the exact file(s) modified
- Provide before/after snippet of the removed section
- Confirm no other UI elements were affected