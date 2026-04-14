USE cms_educational;

INSERT INTO users (username, password, full_name, role)
VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin User', 'admin');

INSERT INTO categories (name) VALUES
('Announcements'),
('Lessons'),
('Events'),
('News');

INSERT INTO tags (name) VALUES
('important'),
('beginner'),
('advanced'),
('featured');

INSERT INTO posts (title, body, status, is_pinned, category_id, user_id) VALUES
('Welcome to our Educational CMS', 'This is the official launch of our educational content management system.', 'published', 1, 1, 1),
('Introduction to PHP', 'PHP is a server-side scripting language used for web development.', 'published', 0, 2, 1),
('Upcoming School Events', 'Check out the list of upcoming events this semester.', 'draft', 0, 3, 1);

INSERT INTO pages (title, body, slug) VALUES
('About Us', 'This is the about page of our educational CMS.', 'about'),
('Contact', 'You can reach us at school@edu.com', 'contact');