-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2026 at 08:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `futurepath_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `application_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `applicant_name` varchar(255) NOT NULL,
  `applicant_email` varchar(255) NOT NULL,
  `resume_file` varchar(255) NOT NULL,
  `applied_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`application_id`, `job_id`, `user_id`, `applicant_name`, `applicant_email`, `resume_file`, `applied_at`) VALUES
(1, 11, 3, 'amal perera', 'amal@gmail.com', 'CV_3_1789758006.pdf', '2026-09-18 19:00:06'),
(2, 11, 8, 'tharindu wishwanath', 'tharindu@gmail.com', 'CV_8_1790413365.pdf', '2026-09-26 09:02:45'),
(4, 14, 11, 'sajith premadasa', 'sajith@gmail.com', 'CV_11_1790420224.pdf', '2026-09-26 10:57:04'),
(5, 13, 11, 'sajith premadasa', 'sajith@gmail.com', 'CV_11_1790420517.pdf', '2026-09-26 11:01:57'),
(6, 14, 11, 'sajith premadasa', 'sajith@gmail.com', 'CV_11_1790420931.pdf', '2026-09-26 11:08:51'),
(7, 13, 3, 'amal perera', 'amal@gmail.com', 'CV_3_1790421710.docx', '2026-09-26 11:21:50'),
(8, 14, 3, 'amal perera', 'amal@gmail.com', 'CV_3_1790421742.pdf', '2026-09-26 11:22:22');

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `counselor_name` varchar(100) NOT NULL,
  `counselor_id` int(11) DEFAULT 1,
  `appointment_date` date NOT NULL,
  `appointment_time` varchar(50) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointments`
--

INSERT INTO `appointments` (`id`, `user_id`, `counselor_name`, `counselor_id`, `appointment_date`, `appointment_time`, `reason`, `status`, `created_at`) VALUES
(1, 3, 'Dr. Rohan Dasanayaka (Engineering)', 1, '2026-09-17', '02:00 PM - 03:00 PM', 'i need to find my career path', 'approved', '2026-09-17 17:35:33'),
(2, 3, 'Mrs. K. Silva (Management Sector)', 1, '2026-09-27', '09:00 AM - 10:00 AM', 'want to know about nvq level courses', 'approved', '2026-09-26 13:01:20');

-- --------------------------------------------------------

--
-- Table structure for table `aptitude_results`
--

CREATE TABLE `aptitude_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `career_id` int(11) NOT NULL,
  `score` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `aptitude_results`
--

INSERT INTO `aptitude_results` (`id`, `user_id`, `career_id`, `score`) VALUES
(15, 11, 11, 60);

-- --------------------------------------------------------

--
-- Table structure for table `careers`
--

CREATE TABLE `careers` (
  `career_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `field` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `required_skills` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`required_skills`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `careers`
--

INSERT INTO `careers` (`career_id`, `title`, `field`, `description`, `required_skills`) VALUES
(7, 'Software Engineer', 'IT', 'Design and develop software applications using modern programming languages and frameworks.', '[\"Programming\", \"Software Development\", \"Problem Solving\", \"Version Control\", \"Logical Thinking\"]'),
(8, 'Full Stack Web Developer', 'IT', 'Build complete web applications from frontend to backend using HTML, CSS, JavaScript, PHP and databases.', '[\"Web Development\", \"Programming\", \"Database Management\", \"Problem Solving\", \"Creativity\"]'),
(9, 'Data Scientist', 'IT', 'Analyze large datasets using statistics, machine learning and programming to extract insights.', '[\"Programming\", \"Analytical Thinking\", \"Database Management\", \"Research Skills\", \"Critical Thinking\"]'),
(10, 'Cybersecurity Analyst', 'IT', 'Protect organizations from cyber threats by monitoring networks and implementing security measures.', '[\"Cybersecurity\", \"Networking\", \"Problem Solving\", \"Attention to Detail\", \"Critical Thinking\"]'),
(11, 'UI/UX Designer', 'IT', 'Design user-friendly digital interfaces focusing on user experience and visual appeal.', '[\"UI/UX Design\", \"Creativity\", \"Empathy\", \"Attention to Detail\", \"Communication\"]'),
(12, 'DevOps Engineer', 'IT', 'Automate deployment pipelines and manage cloud infrastructure for software teams.', '[\"Programming\", \"Networking\", \"Problem Solving\", \"Time Management\", \"Adaptability\"]'),
(13, 'Mobile App Developer', 'IT', 'Create mobile applications for Android and iOS platforms using modern frameworks.', '[\"Programming\", \"Software Development\", \"Creativity\", \"Problem Solving\", \"UI/UX Design\"]'),
(14, 'Civil Engineer', 'Engineering', 'Design and supervise construction of infrastructure like roads, bridges and buildings.', '[\"Analytical Thinking\", \"Project Management\", \"Problem Solving\", \"Attention to Detail\", \"Teamwork\"]'),
(15, 'Mechanical Engineer', 'Engineering', 'Design, analyze and manufacture mechanical systems and machinery.', '[\"Analytical Thinking\", \"Problem Solving\", \"Attention to Detail\", \"Creativity\", \"Technical Communication\"]'),
(16, 'Electrical Engineer', 'Engineering', 'Design and maintain electrical systems, power generation and electronic devices.', '[\"Analytical Thinking\", \"Problem Solving\", \"Attention to Detail\", \"Logical Thinking\", \"Troubleshooting\"]'),
(17, 'Business Analyst', 'Management', 'Analyze business processes and recommend improvements using data-driven insights.', '[\"Analytical Thinking\", \"Communication\", \"Problem Solving\", \"Critical Thinking\", \"Strategic Thinking\"]'),
(18, 'Digital Marketing Specialist', 'Management', 'Plan and execute online marketing campaigns across social media, SEO and paid ads.', '[\"Creativity\", \"Communication\", \"Analytical Thinking\", \"Strategic Thinking\", \"Social Awareness\"]'),
(19, 'Human Resource Manager', 'Management', 'Manage recruitment, employee relations and organizational development.', '[\"Interpersonal Communication\", \"Empathy\", \"Teamwork\", \"Conflict Resolution\", \"Organization\"]'),
(20, 'Project Manager', 'Management', 'Lead projects from initiation to completion, managing scope, time and budget.', '[\"Project Management\", \"Leadership\", \"Time Management\", \"Communication\", \"Decision Making\"]'),
(21, 'Accountant', 'Management', 'Manage financial records, prepare reports and ensure tax compliance.', '[\"Attention to Detail\", \"Analytical Thinking\", \"Organization\", \"Work Ethics\", \"Responsibility\"]'),
(22, 'Entrepreneur', 'Management', 'Start and grow your own business by identifying opportunities and managing risks.', '[\"Leadership\", \"Decision Making\", \"Strategic Thinking\", \"Adaptability\", \"Self-Motivation\"]'),
(23, 'Graphic Designer', 'Design', 'Create visual content for print and digital media using design software.', '[\"Creativity\", \"UI/UX Design\", \"Attention to Detail\", \"Communication\", \"Time Management\"]'),
(24, 'Content Creator', 'Design', 'Produce engaging content for social media, blogs and video platforms.', '[\"Creativity\", \"Written Communication\", \"Verbal Communication\", \"Research Skills\", \"Self-Motivation\"]'),
(25, 'Registered Nurse', 'Healthcare', 'Provide patient care in hospitals and healthcare facilities.', '[\"Empathy\", \"Teamwork\", \"Stress Management\", \"Attention to Detail\", \"Communication\"]'),
(26, 'Teacher / Lecturer', 'Education', 'Educate students in schools, colleges or universities in specialized subjects.', '[\"Communication\", \"Presentation Skills\", \"Patience\", \"Empathy\", \"Continuous Learning\"]');

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `company_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`company_id`, `user_id`, `company_name`, `phone`, `website`, `created_at`) VALUES
(1, 10, 'malaka IT solutions Pvt(Ltd)', NULL, NULL, '2026-09-26 10:11:55');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL,
  `url` varchar(500) DEFAULT NULL,
  `fee` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`course_id`, `course_name`, `description`, `duration`, `url`, `fee`, `created_at`) VALUES
(1, 'Web Development Masterclass', 'Learn Fullstack Web Development with PHP & MySQL', NULL, NULL, 15000.00, '2026-09-17 15:03:29'),
(2, 'Data Science Fundamentals', 'Introduction to Data Analysis, Python, and SQL', NULL, NULL, 20000.00, '2026-09-17 15:03:29'),
(3, 'Cyber Security Basics', 'Learn Network Security and Ethical Hacking concepts', NULL, NULL, 18000.00, '2026-09-17 15:03:29');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `job_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `nvq_level` varchar(50) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `company_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`job_id`, `job_title`, `company_name`, `category`, `nvq_level`, `description`, `qualifications`, `closing_date`, `created_at`, `company_id`) VALUES
(1, 'Trainee Software Developer', 'ABC Tech Solutions', 'IT Sector', 'NVQ Level 5', 'Assister l équipe dans le développement d applications web PHP et MySQL.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(2, 'Assistant Accountant', 'Global Finance Ltd', 'Management', 'NVQ Level 4', 'Gestion de la tenue des comptes et assistance à la préparation des rapports financiers.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(3, 'Junior Web Designer', 'Creative Media Studio', 'IT Sector', 'NVQ Level 4', 'Conception d interfaces utilisateur modernes avec HTML, CSS et Bootstrap.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(4, 'Operations Assistant', 'Apex Logistics', 'Management', 'NVQ Level 5', 'Coordination des opérations quotidiennes et suivi des stocks.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(5, 'Network Technician', 'DataNet Systems', 'IT Sector', 'NVQ Level 6', 'Installation, configuration et maintenance des réseaux informatiques d entreprise.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(6, 'HR Assistant', 'PeopleFirst Corp', 'Management', 'NVQ Level 4', 'Assistance au recrutement, gestion des dossiers des employés et paie.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(7, 'Database Administrator Trainee', 'Enterprise Data Ltd', 'IT Sector', 'NVQ Level 5', 'Gestion de la sécurité des bases de données et optimisation des requêtes SQL.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(8, 'Marketing Executive', 'Visionary Marketing', 'Management', 'NVQ Level 6', 'Planification et exécution des campagnes de marketing digital et traditionnel.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(9, 'Cyber Security Assistant', 'SecureShield Cyber', 'IT Sector', 'NVQ Level 6', 'Surveillance de la sécurité des réseaux et gestion des vulnérabilités.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(10, 'Project Coordinator', 'BuildTech Engineering', 'Management', 'NVQ Level 5', 'Suivi de l avancement des projets et communication avec les clients.', NULL, NULL, '2026-09-18 10:38:30', NULL),
(11, 'software engineer', 'ABC tech solutions', 'IT Sector', 'NVQ Level 6', 'A software engineer designs, builds, tests, and maintains computer programs and applications to solve user needs and business problems\r\n\r\nWe are looking for a skilled Software Engineer to join our team. You will write clean code, work with other teams, and build reliable software.', 'Design & Code: Write clean, efficient code in languages like Java, Python, or C++.\r\nTest & Debug: Find and fix software bugs to improve performance.\r\nCollaborate: Work with product managers and other engineers to plan and build features.\r\nMaintain: Update existing systems and keep them running smoothly.\r\nRequirements: Degree in Computer Science or a related field.\r\nKnowledge of programming languages (e.g., Python, Java, C++).Good problem-solving and teamwork skills.', '2026-10-31', '2026-09-18 14:23:44', NULL),
(13, 'Quality supervisor', 'XYZ constructions Pvt(LTD)', 'Engineering', 'NVQ Level 5', 'Job Summary\r\n\r\nWe are seeking a highly detail-oriented Quality Supervisor to oversee and ensure the highest standards of quality are met on our construction sites. The ideal candidate will be responsible for implementing quality control programs, inspecting materials and workmanship, and ensuring all construction activities comply with project specifications, building codes, and safety regulations.\r\n\r\n****************************************************************\r\n\r\nKey Responsibilities (Job Description)\r\n\r\nQuality Control & Inspection: Conduct regular site inspections to monitor workmanship, materials, and construction methods against approved plans, specifications, and industry standards.\r\n\r\nDocumentation: Maintain accurate records of inspections, test results, non-conformance reports (NCRs), and quality checklists.\r\n\r\nCompliance: Ensure all construction activities comply with local building codes, safety regulations, and environmental standards.\r\n\r\nMaterial Testing: Coordinate and oversee the testing of construction materials (e.g., concrete, soil, steel) to ensure they meet required specifications.\r\n\r\nIssue Resolution: Identify defects, quality issues, and deviations from plans. Work closely with Project Managers, Site Engineers, and Subcontractors to resolve issues promptly.\r\n\r\nSubcontractor Management: Monitor subcontractors\' work to ensure their output meets the project\'s quality benchmarks.\r\n\r\nTraining & Guidance: Educate site staff and workers on quality standards, best practices, and proper installation techniques.\r\n\r\nReporting: Prepare daily, weekly, and monthly quality reports for upper management, highlighting key metrics and areas for improvement.', 'Education & Experience:\r\n\r\nBachelor’s degree or Diploma in Civil Engineering, Construction Management, or a related field.\r\n\r\nMinimum of 5 years of experience in construction quality control, with at least 2 years in a supervisory role.\r\n\r\nProfessional certifications (e.g., CSQA, ISO 9001 Auditor, NEBOSH) are highly advantageous.\r\n\r\nTechnical Skills:\r\n\r\nBlueprint Reading: Ability to read and interpret architectural, structural, and MEP (Mechanical, Electrical, Plumbing) drawings.\r\n\r\nQuality Standards: In-depth knowledge of construction quality standards (e.g., ISO 9001, ASTM, local building codes).\r\n\r\nTesting Procedures: Familiarity with standard construction material testing methods (concrete slump tests, soil compaction, etc.).\r\n\r\nSoftware Proficiency: Proficient in MS Office (Excel, Word, Project) and construction management software (e.g., Procore, AutoCAD, or similar).\r\n\r\nReporting: Experience in writing detailed Non-Conformance Reports (NCRs) and Root Cause Analysis (RCA).\r\n\r\nSoft Skills:\r\n\r\nAttention to Detail: Exceptional ability to spot minor defects and deviations.\r\n\r\nCommunication: Strong verbal and written communication skills to liaise with engineers, architects, and construction crews.\r\n\r\nProblem-Solving: Ability to think critically and resolve on-site issues quickly without delaying the project timeline.\r\n\r\nLeadership: Strong leadership skills to enforce quality standards firmly but diplomatically.\r\n\r\nTime Management: Ability to manage multiple inspection tasks and meet strict project deadlines.', '2026-11-30', '2026-09-26 09:42:55', NULL),
(14, 'Graphic Designer', 'malaka IT solutions Pvt(Ltd)', 'IT Sector', 'NVQ Level 4', 'Job Summary\r\nWe are looking for a creative and detail-oriented Graphic Designer to join our team. You will be responsible for creating engaging visual content across various digital and print platforms. The ideal candidate has a strong portfolio demonstrating a keen eye for aesthetics, typography, and layout, and can translate marketing objectives into compelling design solutions that align with our brand identity.\r\n\r\nKey Responsibilities (Job Description)\r\nVisual Content Creation: Design a wide range of materials, including social media graphics, website banners, email templates, brochures, flyers, and posters.\r\n\r\nBrand Consistency: Ensure all designs adhere to the company’s brand guidelines, maintaining a consistent visual identity across all channels.\r\n\r\nConcept Development: Collaborate with the marketing, content, and product teams to conceptualize and execute new design ideas based on project briefs.\r\n\r\nDigital & Print Production: Prepare final design files for both digital publishing and physical printing, ensuring correct color profiles (CMYK/RGB), bleeds, and resolutions.\r\n\r\nImage Editing: Retouch, resize, and optimize images for various platforms (web, social media, print).\r\n\r\nFeedback & Iteration: Accept constructive feedback from stakeholders and make necessary revisions to designs in a timely manner.\r\n\r\nTrend Awareness: Stay up-to-date with current design trends, software updates, and emerging technologies to keep our brand visuals fresh and modern.', 'Qualifications & Skills\r\nEducation & Experience:\r\n\r\nBachelor’s degree or Diploma in Graphic Design, Visual Arts, Fine Arts, or a related field.\r\n\r\nMinimum of [X] years of professional experience in graphic design (agency or in-house).\r\n\r\nA strong, diverse portfolio showcasing both digital and print design work is mandatory.\r\n\r\nTechnical Skills:\r\n\r\nDesign Software: Expert proficiency in Adobe Creative Suite (Photoshop, Illustrator, InDesign).\r\n\r\nOptional/Plus: Experience with Figma, Canva, or UI/UX design tools (Adobe XD, Sketch).\r\n\r\nWeb & Digital: Basic understanding of web design principles (HTML/CSS is a plus) and experience designing for various social media platforms (Instagram, LinkedIn, Facebook).\r\n\r\nPrinting Process: Knowledge of print production processes, pre-press preparation, and working with print vendors.\r\n\r\nTypography & Layout: Strong understanding of typography, color theory, composition, and visual hierarchy.\r\n\r\nVideo/Motion: Basic video editing skills (e.g., Premiere Pro, After Effects) or basic animation skills are a strong advantage.\r\n\r\nSoft Skills:\r\n\r\nCreativity: Ability to generate original ideas and think outside the box.\r\n\r\nCommunication: Excellent verbal and written communication skills to present and explain design concepts to non-designers.\r\n\r\nTime Management: Ability to manage multiple projects simultaneously, prioritize tasks, and meet strict deadlines.\r\n\r\nAttention to Detail: Meticulous eye for detail and a commitment to delivering high-quality, error-free work.\r\n\r\nCollaboration: Ability to work effectively within a team environment and take direction well.', '2026-12-23', '2026-09-26 10:32:34', 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `slip_image` varchar(255) NOT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `skills_master`
--

CREATE TABLE `skills_master` (
  `skill_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `category_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills_master`
--

INSERT INTO `skills_master` (`skill_id`, `skill_name`, `category_id`) VALUES
(11, 'Problem Solving', 2),
(12, 'Critical Thinking', 2),
(13, 'Logical Thinking', 2),
(14, 'Analytical Thinking', 2),
(15, 'Decision Making', 2),
(16, 'Creativity', 2),
(17, 'Research Skills', 2),
(18, 'Attention to Detail', 2),
(19, 'Strategic Thinking', 2),
(20, 'Learning Ability', 2),
(21, 'Verbal Communication', 3),
(22, 'Written Communication', 3),
(23, 'Active Listening', 3),
(24, 'Presentation Skills', 3),
(25, 'Public Speaking', 3),
(26, 'Technical Communication', 3),
(27, 'Email Communication', 3),
(28, 'Questioning Skills', 3),
(29, 'Information Sharing', 3),
(30, 'Feedback Skills', 3),
(31, 'Teamwork', 4),
(32, 'Collaboration', 4),
(33, 'Empathy', 4),
(34, 'Relationship Building', 4),
(35, 'Conflict Resolution', 4),
(36, 'Cooperation', 4),
(37, 'Respectfulness', 4),
(38, 'Social Awareness', 4),
(39, 'Networking Skills', 4),
(40, 'Interpersonal Communication', 4),
(41, 'Time Management', 5),
(42, 'Work Ethics', 5),
(43, 'Project Management', 5),
(44, 'Organization', 5),
(45, 'Professionalism', 5),
(46, 'Responsibility', 5),
(47, 'Adaptability', 5),
(48, 'Reliability', 5),
(49, 'Goal Setting', 5),
(50, 'Workplace Discipline', 5),
(51, 'Self-Discipline', 6),
(52, 'Self-Motivation', 6),
(53, 'Self-Awareness', 6),
(54, 'Stress Management', 6),
(55, 'Emotional Control', 6),
(56, 'Goal Management', 6),
(57, 'Focus', 6),
(58, 'Patience', 6),
(59, 'Confidence', 6),
(60, 'Continuous Learning', 6),
(61, 'Programming', 1),
(62, 'Web Development', 1),
(63, 'Database Management', 1),
(64, 'Networking', 1),
(65, 'Cybersecurity', 1),
(66, 'Software Testing', 1),
(67, 'AutoCAD', 1),
(68, 'Technical Drawing', 1),
(69, 'Surveying', 1),
(70, 'Structural Analysis', 1),
(71, 'Quantity Surveying', 1),
(72, 'Construction Technology', 1),
(73, 'Patient Care', 1),
(74, 'Medical Laboratory Techniques', 1),
(75, 'Medical Imaging', 1),
(76, 'First Aid', 1),
(77, 'Clinical Documentation', 1),
(78, 'Medical Equipment Handling', 1),
(79, 'Accounting', 1),
(80, 'Financial Analysis', 1),
(81, 'Bookkeeping', 1),
(82, 'Taxation', 1),
(83, 'Budgeting', 1),
(84, 'Financial Software', 1),
(85, 'Graphic Design', 1),
(86, 'Video Editing', 1),
(87, 'Photography', 1),
(88, 'UI/UX Design', 1),
(89, 'Animation', 1),
(90, 'Digital Illustration', 1),
(91, 'Food Preparation', 1),
(92, 'Food & Beverage Service', 1),
(93, 'Hotel Management Systems', 1),
(94, 'Housekeeping Operations', 1),
(95, 'Front Office Operations', 1),
(96, 'Event Management', 1),
(97, 'Crop Management', 1),
(98, 'Irrigation Techniques', 1),
(99, 'Soil Testing', 1),
(100, 'Pest Management', 1),
(101, 'Agricultural Machinery Operation', 1),
(102, 'Farm Management', 1),
(103, 'Vehicle Diagnostics', 1),
(104, 'Engine Repair', 1),
(105, 'Auto Electrical Systems', 1),
(106, 'Vehicle Maintenance', 1),
(107, 'Brake System Repair', 1),
(108, 'Automotive Welding', 1),
(109, 'Lesson Planning', 1),
(110, 'Classroom Management', 1),
(111, 'Educational Technology', 1),
(112, 'Assessment Techniques', 1),
(113, 'Curriculum Development', 1),
(114, 'Learning Management Systems', 1),
(115, 'Hair Styling', 1),
(116, 'Hair Cutting', 1),
(117, 'Hair Coloring', 1),
(118, 'Makeup Application', 1),
(119, 'Nail Care', 1),
(120, 'Skin Care', 1);

-- --------------------------------------------------------

--
-- Table structure for table `skill_categories`
--

CREATE TABLE `skill_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(50) NOT NULL,
  `category_icon` varchar(50) DEFAULT NULL,
  `display_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skill_categories`
--

INSERT INTO `skill_categories` (`category_id`, `category_name`, `category_icon`, `display_order`) VALUES
(1, 'Technical', '💻', 1),
(2, 'Cognitive', '🧠', 2),
(3, 'Communication', '🗣️', 3),
(4, 'Interpersonal', '🤝', 4),
(5, 'Professional', '👔', 5),
(6, 'Self-Management', '🌱', 6);

-- --------------------------------------------------------

--
-- Table structure for table `student_academic`
--

CREATE TABLE `student_academic` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `stream` varchar(50) NOT NULL,
  `gpa` decimal(3,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_academic`
--

INSERT INTO `student_academic` (`id`, `user_id`, `stream`, `gpa`) VALUES
(10, 11, 'Engineering', 4.00);

-- --------------------------------------------------------

--
-- Table structure for table `test_results`
--

CREATE TABLE `test_results` (
  `result_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('student','counselor','admin','company') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(2, 'sithum sathsara dissanayake', 'dissanayakesms@gmail.com', '$2y$10$CCvy0Yct1nbgdED1kElYGOLPP2.kGGXjFqGvehgUgTBidWOZWXfHS', 'admin', '2026-09-17 14:55:32'),
(3, 'amal perera', 'amal@gmail.com', '$2y$10$aYrr11UVN2Pflk1BWe1MJOVxFnqkWHCEhb8FMmsE6s7RJ2WYi154O', 'student', '2026-09-17 15:46:02'),
(4, 'chamal rajapaksha', 'chamal@gmail.com', '$2y$10$9RYaEhiBvpjtS8N7P5t3VOhxLy3bZUl.QeqkiZOhcmbmHwuUEEA0y', 'counselor', '2026-09-18 15:32:13'),
(7, 'UNIVOTEC', 'univotec@gmail.com', '$2y$10$nL/FnKcjw0dVPwhoSYc5sex4LluiOS9.ge9YKBChmasC3yUS81t.W', 'company', '2026-09-18 18:10:50'),
(8, 'tharindu wishwanath', 'tharindu@gmail.com', '$2y$10$5k.jKUmew2jwKXPkfxkyWOfVYfBxUXtma/xziZTe4lmHaljQDwszG', 'student', '2026-09-26 08:57:01'),
(9, 'XYZ constructions Pvt(LTD)', 'XYZconstructions@gmail.com', '$2y$10$vGH72z8i9.SWgRkT07lutuddUmAgQXUkD6PPkGtwZwfgj2Qp44JUa', 'company', '2026-09-26 09:16:24'),
(10, 'malaka IT solutions Pvt(Ltd)', 'malakaIT@gmail.com', '$2y$10$c9tOPxxvAHw.8bP/A0vo5uFaeyszcaGf1ZG34Lg5SxoLtCc3J4EH.', 'company', '2026-09-26 10:11:55'),
(11, 'sajith premadasa', 'sajith@gmail.com', '$2y$10$50Ub4aKmmd7fJXJMZXyVh.hKa9TnOZLUGHuMyUQbWVYGnXC1vP01K', 'student', '2026-09-26 10:55:49'),
(12, 'sirimawo bandaranayake', 'sirimawo@gmail.com', '$2y$10$klTaM/4O6aRELkjGL5IEhuWi/Kwfzgp5873Ss8XNc87r/vzbrEUie', 'counselor', '2026-09-26 11:54:10'),
(13, 'G.R.C. Kumara', 'grc.atitangale@gmail.com', '$2y$10$pVjL.7/xrh88vwN49kXLCeCywQu2HW4a1v6LCCvTTUb7AJu9go5He', 'counselor', '2026-09-26 13:05:10');

-- --------------------------------------------------------

--
-- Table structure for table `user_skills`
--

CREATE TABLE `user_skills` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `skill_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_skills`
--

INSERT INTO `user_skills` (`id`, `user_id`, `skill_name`) VALUES
(61, 11, 'Cybersecurity'),
(62, 11, 'Database Management'),
(63, 11, 'UI/UX Design'),
(64, 11, 'Web Development'),
(65, 11, 'Analytical Thinking'),
(66, 11, 'Creativity'),
(67, 11, 'Decision Making'),
(68, 11, 'Problem Solving'),
(69, 11, 'Strategic Thinking'),
(70, 11, 'Active Listening'),
(71, 11, 'Presentation Skills'),
(72, 11, 'Public Speaking'),
(73, 11, 'Questioning Skills'),
(74, 11, 'Technical Communication'),
(75, 11, 'Collaboration'),
(76, 11, 'Cooperation'),
(77, 11, 'Empathy'),
(78, 11, 'Relationship Building'),
(79, 11, 'Social Awareness'),
(80, 11, 'Teamwork'),
(81, 11, 'Organization'),
(82, 11, 'Professionalism'),
(83, 11, 'Project Management'),
(84, 11, 'Reliability'),
(85, 11, 'Workplace Discipline'),
(86, 11, 'Confidence'),
(87, 11, 'Focus'),
(88, 11, 'Goal Management'),
(89, 11, 'Self-Discipline'),
(90, 11, 'Self-Motivation'),
(91, 11, 'Stress Management');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`application_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `aptitude_results`
--
ALTER TABLE `aptitude_results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `career_id` (`career_id`);

--
-- Indexes for table `careers`
--
ALTER TABLE `careers`
  ADD PRIMARY KEY (`career_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`company_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`job_id`),
  ADD KEY `fk_jobs_company` (`company_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `skills_master`
--
ALTER TABLE `skills_master`
  ADD PRIMARY KEY (`skill_id`),
  ADD UNIQUE KEY `skill_name` (`skill_name`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `skill_categories`
--
ALTER TABLE `skill_categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `student_academic`
--
ALTER TABLE `student_academic`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `test_results`
--
ALTER TABLE `test_results`
  ADD PRIMARY KEY (`result_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `user_skills`
--
ALTER TABLE `user_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `application_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `aptitude_results`
--
ALTER TABLE `aptitude_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `careers`
--
ALTER TABLE `careers`
  MODIFY `career_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `company_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `job_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skills_master`
--
ALTER TABLE `skills_master`
  MODIFY `skill_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=121;

--
-- AUTO_INCREMENT for table `skill_categories`
--
ALTER TABLE `skill_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_academic`
--
ALTER TABLE `student_academic`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `test_results`
--
ALTER TABLE `test_results`
  MODIFY `result_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `user_skills`
--
ALTER TABLE `user_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`job_id`) ON DELETE CASCADE;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `aptitude_results`
--
ALTER TABLE `aptitude_results`
  ADD CONSTRAINT `aptitude_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `aptitude_results_ibfk_2` FOREIGN KEY (`career_id`) REFERENCES `careers` (`career_id`) ON DELETE CASCADE;

--
-- Constraints for table `companies`
--
ALTER TABLE `companies`
  ADD CONSTRAINT `companies_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `fk_jobs_company` FOREIGN KEY (`company_id`) REFERENCES `companies` (`company_id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `skills_master`
--
ALTER TABLE `skills_master`
  ADD CONSTRAINT `skills_master_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `skill_categories` (`category_id`) ON DELETE CASCADE;

--
-- Constraints for table `student_academic`
--
ALTER TABLE `student_academic`
  ADD CONSTRAINT `student_academic_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `test_results`
--
ALTER TABLE `test_results`
  ADD CONSTRAINT `test_results_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `user_skills`
--
ALTER TABLE `user_skills`
  ADD CONSTRAINT `user_skills_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
