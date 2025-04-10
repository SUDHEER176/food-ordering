<?php
include 'db.php';
session_start();
$result = mysqli_query($conn, "SELECT * FROM food_items");

// Define food categories
$categories = [
    ['name' => 'Pizza', 'icon' => '🍕'],
    ['name' => 'Burger', 'icon' => '🍔'],
    ['name' => 'Sushi', 'icon' => '🍣'],
    ['name' => 'Noodles', 'icon' => '🍜'],
    ['name' => 'Indian', 'icon' => '🍛'],
    ['name' => 'Mexican', 'icon' => '🌮'],
    ['name' => 'Desserts', 'icon' => '🍦'],
    ['name' => 'Bakery', 'icon' => '🥖']
];

// Define featured restaurants
$restaurants = [
    [
        'name' => 'The Grand Kitchen',
        'image' => 'images/restaurants/3.jpg',
        'rating' => '4.8',
        'delivery_time' => '25-30 min',
        'cuisine' => 'Italian, Continental',
        'price' => '₹500 for two',
        'promotion' => '20% OFF',
        'free_delivery' => true,
        'promoted' => true
    ],
    [
        'name' => 'The Grand Kitchen',
        'image' => 'images/restaurants/2.jpg',
        'rating' => '4.8',
        'delivery_time' => '25-30 min',
        'cuisine' => 'Italian, Continental',
        'price' => '₹500 for two',
        'promotion' => '20% OFF',
        'free_delivery' => true,
        'promoted' => true
    ],
    [
        'name' => 'The Grand Kitchen',
        'image' => 'images/restaurants/1.jpg',
        'rating' => '4.8',
        'delivery_time' => '25-30 min',
        'cuisine' => 'Italian, Continental',
        'price' => '₹500 for two',
        'promotion' => '20% OFF',
        'free_delivery' => true,
        'promoted' => true
    ],
    [
        'name' => 'The Spice Route',
        'image' => 'images/restaurants/3.jpg',
        'rating' => '4.7',
        'delivery_time' => '20-25 min',
        'cuisine' => 'South Indian, Chinese',
        'price' => '₹300 for two',
        'promotion' => '',
        'free_delivery' => true,
        'promoted' => true
    ],
    [
        'name' => 'Spice Garden',
        'image' => 'images/restaurants/1.jpg',
        'rating' => '4.5',
        'delivery_time' => '35-40 min',
        'cuisine' => 'North Indian, Mughlai',
        'price' => '₹400 for two',
        'promotion' => '',
        'free_delivery' => true,
        'promoted' => false
    ],
    [
        'name' => 'Dragon House',
        'image' => 'images/restaurants/3.jpg',
        'rating' => '4.3',
        'delivery_time' => '30-35 min',
        'cuisine' => 'Chinese, Thai',
        'price' => '₹600 for two',
        'promotion' => '15% OFF',
        'free_delivery' => false,
        'promoted' => false
    ]
];

// Define testimonials
$testimonials = [
    [
        'name' => 'Sarah Johnson',
        'location' => 'New Delhi',
        'rating' => 5,
        'comment' => 'The food arrived hot and fresh. The delivery was faster than expected. Will definitely order again!'
    ],
    [
        'name' => 'Rahul Sharma',
        'location' => 'Mumbai',
        'rating' => 4.5,
        'comment' => 'Great selection of restaurants. I love how I can track my order in real-time. The app is very user-friendly.'
    ],
    [
        'name' => 'Priya Patel',
        'location' => 'Bangalore',
        'rating' => 5,
        'comment' => 'Food Express has been a lifesaver during busy work weeks. The quality of food and service is consistently excellent.'
    ],
    [
        'name' => 'Vikram Singh',
        'location' => 'Hyderabad',
        'rating' => 5,
        'comment' => 'The variety of cuisines available is amazing. I\'ve discovered so many new favorite restaurants through Food Express.'
    ]
];

// Get cart count
$cart_count = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Express</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', Arial, sans-serif;
        }
        
        body {
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }
        
        a {
            text-decoration: none;
            color: inherit;
        }
        
        button {
            cursor: pointer;
            border: none;
            outline: none;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        
        /* Header Styles */
        .header {
            background-color: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 0;
        }
        
        .logo {
            display: flex;
            align-items: center;
            font-size: 24px;
            font-weight: bold;
            color: #ff9800;
        }
        
        .logo img, .logo i {
            margin-right: 10px;
            font-size: 28px;
        }
        
        .search-bar {
            flex: 1;
            max-width: 500px;
            margin: 0 20px;
            position: relative;
        }
        
        .search-bar input {
            width: 100%;
            padding: 12px 20px;
            padding-left: 40px;
            border: 1px solid #ddd;
            border-radius: 30px;
            font-size: 14px;
            outline: none;
        }
        
        .search-bar i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #777;
        }
        
        .header-actions {
            display: flex;
            align-items: center;
        }
        
        .location-btn, .cart-btn, .profile-btn {
            display: flex;
            align-items: center;
            margin-left: 15px;
            padding: 8px 12px;
            border-radius: 20px;
            transition: all 0.3s ease;
        }
        
        .location-btn:hover, .cart-btn:hover {
            background-color: #f0f0f0;
        }
        
        .location-btn i, .cart-btn i {
            margin-right: 5px;
            font-size: 18px;
        }
        
        .cart-btn {
            position: relative;
        }
        
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #ff5722;
            color: white;
            font-size: 12px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .profile-btn {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .profile-btn img {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 8px;
        }
        
        .profile-btn i {
            margin-left: 5px;
            transition: transform 0.3s ease;
        }
        
        .profile-btn.active i {
            transform: rotate(180deg);
        }
        
        /* Profile Dropdown */
        .profile-dropdown {
            position: absolute;
            top: 70px;
            right: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            width: 250px;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s ease;
        }
        
        .profile-dropdown.active {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            transition: background-color 0.3s ease;
        }
        
        .dropdown-item:last-child {
            border-bottom: none;
        }
        
        .dropdown-item:hover {
            background-color: #f8f9fa;
        }
        
        .dropdown-item i {
            margin-right: 15px;
            font-size: 18px;
            width: 20px;
            text-align: center;
        }
        
        /* Hero Section */
       /* Hero Section */
.hero {
    background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), 
                url('https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    height: 500px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    color: white;
    padding: 0 20px;
    position: relative;
}
        
        
        .hero h1 {
            font-size: 48px;
            margin-bottom: 20px;
            animation: fadeInDown 1s ease;
            line-height: 1.2;
        }
        
        .hero p {
            font-size: 18px;
            margin-bottom: 30px;
            max-width: 600px;
            animation: fadeInUp 1s ease;
        }
        
        .location-search {
            display: flex;
            width: 100%;
            max-width: 600px;
            margin-top: 30px;
            animation: fadeIn 1.5s ease;
        }
        
        .location-search input {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: 5px 0 0 5px;
            font-size: 16px;
            outline: none;
        }
        
        .location-search button {
            padding: 15px 30px;
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 0 5px 5px 0;
            font-size: 16px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        
        .location-search button:hover {
            background-color: #f57c00;
        }
        
        .hero-slider-dots {
            position: absolute;
            bottom: 30px;
            display: flex;
            gap: 10px;
        }
        
        .dot {
            width: 12px;
            height: 12px;
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .dot.active {
            background-color: white;
            transform: scale(1.2);
        }
        
        /* Features Section */
        .features {
            padding: 80px 0;
            background-color: #fff;
        }
        
        .section-title {
            text-align: center;
            font-size: 32px;
            margin-bottom: 15px;
            color: #333;
        }
        
        .section-subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 50px;
            font-size: 16px;
        }
        
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        
        .feature {
            text-align: center;
            padding: 40px 25px;
            border-radius: 10px;
            background-color: #f9f9f9;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .feature:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }
        
        .feature-icon {
            font-size: 2.5rem;
            color: #ff9800;
            margin-bottom: 20px;
        }
        
        .feature h3 {
            font-size: 1.2rem;
            margin-bottom: 15px;
            color: #333;
        }
        
        .feature p {
            color: #666;
            font-size: 0.95rem;
        }
        
        /* Categories Section */
        .section {
            padding: 80px 0;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 50px;
        }
        
        .section-header h2 {
            font-size: 32px;
            color: #333;
            margin-bottom: 15px;
        }
        
        .section-header p {
            font-size: 16px;
            color: #777;
        }
        
        .categories {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        
        .category {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 120px;
            text-align: center;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .category:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .category-icon {
            font-size: 36px;
            margin-bottom: 10px;
        }
        
        .category-name {
            font-weight: 500;
        }
        
        /* Restaurants Section */
        .view-all {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .view-all a {
            color: #ff9800;
            font-weight: 500;
            display: flex;
            align-items: center;
            transition: color 0.3s ease;
        }
        
        .view-all a:hover {
            color: #f57c00;
        }
        
        .view-all a i {
            margin-left: 5px;
        }
        
        .restaurants {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
        }
        
        .restaurant-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .restaurant-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .restaurant-image {
            position: relative;
            height: 200px;
            overflow: hidden;
        }
        
        .restaurant-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .restaurant-card:hover .restaurant-image img {
            transform: scale(1.05);
        }
        
        .restaurant-rating {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: white;
            border-radius: 20px;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            font-weight: bold;
        }
        
        .restaurant-rating i {
            color: #ffc107;
            margin-right: 5px;
        }
        
        .delivery-time {
            position: absolute;
            top: 15px;
            right: 15px;
            background-color: white;
            border-radius: 20px;
            padding: 5px 10px;
            font-weight: 500;
        }
        
        .restaurant-promo {
            position: absolute;
            bottom: 15px;
            left: 15px;
            display: flex;
            gap: 10px;
        }
        
        .promo-tag {
            background-color: #4caf50;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .discount-tag {
            background-color: #ff5722;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .restaurant-info {
            padding: 20px;
        }
        
        .restaurant-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
        }
        
        .promoted {
            color: #ff9800;
            font-size: 12px;
            font-weight: normal;
        }
        
        .restaurant-cuisine {
            color: #777;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .restaurant-price {
            color: #555;
            font-weight: 500;
            font-size: 15px;
        }
        
        /* Popular Dishes Section */
        .dishes {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 30px;
        }
        
        .dish-card {
            background-color: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
        }
        
        .dish-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        }
        
        .dish-image {
            position: relative;
            height: 180px;
            overflow: hidden;
        }
        
        .dish-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .dish-card:hover .dish-image img {
            transform: scale(1.05);
        }
        
        .dish-tag {
            position: absolute;
            top: 15px;
            left: 15px;
            background-color: #ff9800;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .dish-rating {
            position: absolute;
            bottom: 15px;
            left: 15px;
            background-color: white;
            border-radius: 20px;
            padding: 5px 10px;
            display: flex;
            align-items: center;
            font-weight: bold;
        }
        
        .dish-rating i {
            color: #ffc107;
            margin-right: 5px;
        }
        
        .dish-time {
            position: absolute;
            bottom: 15px;
            right: 15px;
            background-color: white;
            border-radius: 20px;
            padding: 5px 10px;
            font-weight: 500;
        }
        
        .dish-info {
            padding: 20px;
        }
        
        .dish-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .dish-restaurant {
            color: #777;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .dish-price-action {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .dish-price {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        
        .add-btn {
            background-color: #ff9800;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: bold;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
        }
        
        .add-btn:hover {
            background-color: #f57c00;
        }
        
        .add-btn i {
            margin-right: 5px;
        }
        
        /* Testimonials Section */
.testimonials-section {
    padding: 80px 0;
    background-color: #f9f9f9;
}

.testimonials-container {
    position: relative;
    padding: 0 50px;
    max-width: 1200px;
    margin: 0 auto;
}

.testimonials {
    display: flex;
    overflow-x: auto;
    scroll-snap-type: x mandatory;
    gap: 30px;
    padding: 30px 0;
    scrollbar-width: none; /* For Firefox */
    -ms-overflow-style: none; /* For IE and Edge */
}

.testimonials::-webkit-scrollbar {
    display: none; /* For Chrome, Safari and Opera */
}

.testimonial-card {
    min-width: 350px;
    scroll-snap-align: start;
    background-color: white;
    border-radius: 10px;
    padding: 40px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    position: relative;
}

.testimonial-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
}

.testimonial-header {
    display: flex;
    align-items: center;
    margin-bottom: 25px;
}

.testimonial-avatar {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    overflow: hidden;
    margin-right: 20px;
    border: 3px solid #ff9800;
}

.testimonial-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.testimonial-user {
    flex: 1;
}

.testimonial-name {
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
    font-size: 18px;
}

.testimonial-location {
    color: #777;
    font-size: 14px;
}

.testimonial-rating {
    display: flex;
    margin-bottom: 20px;
}

.testimonial-rating i {
    color: #ffc107;
    margin-right: 5px;
    font-size: 16px;
}

.testimonial-text {
    color: #555;
    line-height: 1.8;
    font-size: 16px;
    position: relative;
    padding-left: 20px;
}

.testimonial-text::before {
    content: '"';
    position: absolute;
    left: 0;
    top: -10px;
    font-size: 50px;
    color: rgba(255, 152, 0, 0.1);
    font-family: serif;
    line-height: 1;
}

.testimonial-nav {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    width: calc(100% - 100px);
    left: 50px;
    display: flex;
    justify-content: space-between;
    z-index: 1;
}

.nav-btn {
    width: 50px;
    height: 50px;
    background-color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: all 0.3s ease;
    color: #333;
}

.nav-btn:hover {
    background-color: #ff9800;
    color: white;
    transform: scale(1.1);
}

.testimonial-quote {
    position: absolute;
    right: 30px;
    bottom: 30px;
    color: rgba(255, 152, 0, 0.2);
    font-size: 60px;
    line-height: 1;
    font-family: serif;
}
        /* Footer */
footer {
    background-color: #222;
    color: white;
    padding: 70px 0 20px;
}

.footer-content {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 40px;
    margin-bottom: 40px;
}

.footer-section {
    margin-bottom: 20px;
}

.footer-section h3 {
    font-size: 20px;
    margin-bottom: 25px;
    position: relative;
    padding-bottom: 10px;
    color: #fff;
}

.footer-section h3::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 3px;
    background-color: #ff9800;
}

.footer-links {
    list-style: none;
}

.footer-links li {
    margin-bottom: 12px;
}

.footer-links a {
    color: #bbb;
    transition: all 0.3s ease;
    display: inline-block;
    font-size: 15px;
}

.footer-links a:hover {
    color: #ff9800;
    transform: translateX(5px);
}

.footer-links i {
    margin-right: 10px;
    color: #ff9800;
    width: 20px;
    text-align: center;
}

.social-links {
    display: flex;
    gap: 15px;
    margin-top: 25px;
}

.social-links a {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background-color: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    transition: all 0.3s ease;
    color: #fff;
}

.social-links a:hover {
    background-color: #ff9800;
    transform: translateY(-5px);
}

.app-links {
    display: flex;
    flex-direction: column;
    gap: 15px;
    margin-top: 20px;
}

.app-links a {
    display: block;
    width: 160px;
    transition: transform 0.3s ease;
}

.app-links a:hover {
    transform: scale(1.05);
}

.app-links img {
    width: 100%;
    border-radius: 5px;
}

.footer-bottom {
    text-align: center;
    padding-top: 30px;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
    font-size: 14px;
    color: #aaa;
}
        </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <a href="index.php" class="logo">
                    <i class="fas fa-utensils"></i>
                    Food Express
                </a>
                
                <div class="search-bar">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search for food, cuisines, restaurants...">
                </div>
                
                <div class="header-actions">
                    <a href="#" class="location-btn">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Location</span>
                    </a>
                    
                    <a href="cart.php" class="cart-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>Cart</span>
                        <?php if($cart_count > 0): ?>
                            <div class="cart-count"><?= $cart_count ?></div>
                        <?php endif; ?>
                    </a>
                    
                    <div class="profile-btn" id="profileBtn">
                        <?php if(isset($_SESSION['username'])): ?>
                            
                            <span>Profile</span>
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                            <span>Profile</span>
                        <?php endif; ?>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Profile Dropdown -->
    <div class="profile-dropdown" id="profileDropdown">
        <?php if(isset($_SESSION['username'])): ?>
            <a href="profile.php" class="dropdown-item">
                <i class="fas fa-user"></i>
                My Profile
            </a>
            <a href="logout.php" class="dropdown-item">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        <?php else: ?>
            <a href="login.php" class="dropdown-item">
                <i class="fas fa-sign-in-alt"></i>
                Login
            </a>
            <a href="register.php" class="dropdown-item">
                <i class="fas fa-user-plus"></i>
                Register
            </a>
        <?php endif; ?>
    </div>
    <!-- Hero Section -->
<section class="hero">
    <h1>Delicious Food Delivered To Your Door</h1>
    <p>Order from the best local restaurants with easy, on-demand delivery. Discover new cuisines and explore menus from top-rated restaurants in your area.</p>
    
    <div class="location-search">
        <input type="text" placeholder="Enter your delivery location">
        <button type="submit">Find Food</button>
    </div>
   
</section> <!-- Features Section -->
    <section class="features">
        <div class="container">
            <h2 class="section-title">Why Choose Food Express?</h2>
            <p class="section-subtitle">We're more than just food delivery</p>
            
            <div class="features-grid">
                <div class="feature">
                    <i class="fas fa-clock feature-icon"></i>
                    <h3>Lightning-Fast Delivery</h3>
                    <p>Experience our super-fast delivery for food delivered fresh & on time</p>
                </div>
    
                <div class="feature">
                    <i class="fas fa-map-marked-alt feature-icon"></i>
                    <h3>Live Order Tracking</h3>
                    <p>Know where your order is at all times, from the restaurant to your doorstep</p>
                </div>
    
                <div class="feature">
                    <i class="fas fa-store feature-icon"></i>
                    <h3>Wide Selection</h3>
                    <p>Choose from over 9000+ restaurants and home chefs</p>
                </div>
                
                <div class="feature">
                    <i class="fas fa-shield-alt feature-icon"></i>
                    <h3>Secure Payments</h3>
                    <p>Multiple secure payment options for hassle-free transactions</p>
                </div>
            </div>
        </div>
    </section>
    
       <!-- Categories Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Popular Food Categories</h2>
                <p>Explore our wide range of delicious options</p>
            </div>
            
            <div class="categories">
                <?php foreach($categories as $category): ?>
                <a href="category.php?name=<?= urlencode($category['name']) ?>" class="category fade-in-up">
                    <div class="category-icon"><?= $category['icon'] ?></div>
                    <div class="category-name"><?= $category['name'] ?></div>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<!-- Featured Restaurants Section -->
<section class="section">
    <div class="container">
        <div class="view-all">
            <div class="section-header" style="margin-bottom: 0;">
                <h2>Featured Restaurants</h2>
            </div>
            <a href="restaurants.php">View All <i class="fas fa-chevron-right"></i></a>
        </div>
        
        <div class="restaurants">
            <?php foreach($restaurants as $restaurant): 
                // Set default values if keys don't exist
                $name = $restaurant['name'] ?? '';
                $image = $restaurant['image'] ?? '';
                $rating = $restaurant['rating'] ?? '0.0';
                $delivery_time = $restaurant['delivery_time'] ?? 'N/A';
                $free_delivery = $restaurant['free_delivery'] ?? false;
                $promotion = $restaurant['promotion'] ?? '';
                $cuisine = $restaurant['cuisine'] ?? '';
                $price = $restaurant['price'] ?? '';
                $promoted = $restaurant['promoted'] ?? false;
            ?>
            <div class="restaurant-card fade-in-up">
                <div class="restaurant-image">
                    <img src="<?= htmlspecialchars($image) ?>" alt="<?= htmlspecialchars($name) ?>">
                    <?php if(isset($rating)): ?>
                        <div class="restaurant-rating">
                            <i class="fas fa-star"></i> <?= htmlspecialchars($rating) ?>
                        </div>
                    <?php endif; ?>
                    <?php if(isset($delivery_time)): ?>
                        <div class="delivery-time"><?= htmlspecialchars($delivery_time) ?></div>
                    <?php endif; ?>
                    <div class="restaurant-promo">
                        <?php if($free_delivery): ?>
                            <div class="promo-tag">Free Delivery</div>
                        <?php endif; ?>
                        <?php if(!empty($promotion)): ?>
                            <div class="discount-tag"><?= htmlspecialchars($promotion) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="restaurant-info">
                    <div class="restaurant-name">
                        <?= htmlspecialchars($name) ?>
                        <?php if($promoted): ?>
                            <span class="promoted">Promoted</span>
                        <?php endif; ?>
                    </div>
                    <?php if(!empty($cuisine)): ?>
                        <div class="restaurant-cuisine"><?= htmlspecialchars($cuisine) ?></div>
                    <?php endif; ?>
                    <?php if(!empty($price)): ?>
                        <div class="restaurant-price"><?= htmlspecialchars($price) ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
    <!-- Popular Dishes Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>Popular Dishes</h2>
                <p>Most ordered dishes by our customers</p>
            </div>
            
            <div class="dishes">
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <div class="dish-card fade-in-up">
                    <div class="dish-image">
                        <img src="<?= $row['image']; ?>" alt="<?= $row['name']; ?>">
                        <div class="dish-tag">Popular</div>
                        <div class="dish-rating">
                            <i class="fas fa-star"></i> 4.8
                        </div>
                        <div class="dish-time">20-30 min</div>
                    </div>
                    <div class="dish-info">
                        <div class="dish-name"><?= $row['name']; ?></div>
                        <div class="dish-restaurant">Restaurant Name</div>
                        <div class="dish-price-action">
                            <div class="dish-price">₹<?= $row['price']; ?></div>
                            <form method="post" action="cart.php">
                                <input type="hidden" name="food_id" value="<?= $row['id']; ?>">
                                <button type="submit" name="add_to_cart" class="add-btn">
                                    <i class="fas fa-plus"></i> Add
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </div>
    </section>
    
    <!-- Testimonials Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2>What Our Customers Say</h2>
                <p>Real experiences from food lovers like you</p>
            </div>
            
            <div class="testimonials-container">
                <div class="testimonials" id="testimonials">
                    <?php foreach($testimonials as $testimonial): ?>
                    <div class="testimonial-card fade-in-up">
                        <div class="testimonial-header">
                            <div class="testimonial-avatar">
                                <img src="images/avatars/<?= strtolower(str_replace(' ', '-', $testimonial['name'])) ?>.jpg" alt="<?= $testimonial['name'] ?>">
                            </div>
                            <div class="testimonial-user">
                                <div class="testimonial-name"><?= $testimonial['name'] ?></div>
                                <div class="testimonial-location"><?= $testimonial['location'] ?></div>
                            </div>
                        </div>
                        <div class="testimonial-rating">
                            <?php for($i = 1; $i <= 5; $i++): ?>
                                <?php if($i <= $testimonial['rating']): ?>
                                    <i class="fas fa-star"></i>
                                <?php elseif($i - 0.5 <= $testimonial['rating']): ?>
                                    <i class="fas fa-star-half-alt"></i>
                                <?php else: ?>
                                    <i class="far fa-star"></i>
                                <?php endif; ?>
                            <?php endfor; ?>
                        </div>
                        <div class="testimonial-text">
                            "<?= $testimonial['comment'] ?>"
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="testimonial-nav">
                    <div class="nav-btn prev-btn">
                        <i class="fas fa-chevron-left"></i>
                    </div>
                    <div class="nav-btn next-btn">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3>Food Express</h3>
                    <p>Delicious food delivered to your doorstep. Order now and experience the best food delivery service.</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
                
                <div class="footer-section">
                    <h3>Quick Links</h3>
                    <ul class="footer-links">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="restaurants.php">Restaurants</a></li>
                        <li><a href="about.php">About Us</a></li>
                        <li><a href="contact.php">Contact Us</a></li>
                        <li><a href="faq.php">FAQ</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Contact Us</h3>
                    <ul class="footer-links">
                        <li><i class="fas fa-map-marker-alt"></i> 123 Food Street, Foodville</li>
                        <li><i class="fas fa-phone"></i> +91 1234567890</li>
                        <li><i class="fas fa-envelope"></i> info@foodexpress.com</li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Download App</h3>
                    <p>Get our mobile app for a better experience</p>
                    <div class="app-links">
                        <a href="#"><img src="images/app-store.png" alt="App Store"></a>
                        <a href="#"><img src="images/play-store.png" alt="Play Store"></a>
                    </div>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date("Y") ?> Food Express. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script>
        // Profile Dropdown Toggle
        document.addEventListener('DOMContentLoaded', function() {
            const profileBtn = document.getElementById('profileBtn');
            const profileDropdown = document.getElementById('profileDropdown');
            
            profileBtn.addEventListener('click', function() {
                profileBtn.classList.toggle('active');
                profileDropdown.classList.toggle('active');
            });
            
            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                if (!profileBtn.contains(event.target) && !profileDropdown.contains(event.target)) {
                    profileBtn.classList.remove('active');
                    profileDropdown.classList.remove('active');
                }
            });
            
            // Testimonials Slider
            const testimonials = document.getElementById('testimonials');
            const prevBtn = document.querySelector('.prev-btn');
            const nextBtn = document.querySelector('.next-btn');
            
            prevBtn.addEventListener('click', function() {
                testimonials.scrollBy({ left: -330, behavior: 'smooth' });
            });
            
            nextBtn.addEventListener('click', function() {
                testimonials.scrollBy({ left: 330, behavior: 'smooth' });
            });
            
            // Hero Slider Dots
            const dots = document.querySelectorAll('.dot');
            let currentSlide = 0;
            
            function changeSlide(index) {
                dots.forEach(dot => dot.classList.remove('active'));
                dots[index].classList.add('active');
                
                // Here you would normally change the background image
                // For demo purposes, we're just changing the active dot
            }
            
            dots.forEach((dot, index) => {
                dot.addEventListener('click', function() {
                    changeSlide(index);
                    currentSlide = index;
                });
            });
            
            // Auto change slide every 5 seconds
            setInterval(function() {
                currentSlide = (currentSlide + 1) % dots.length;
                changeSlide(currentSlide);
            }, 5000);
            
            // Animation on scroll
            const animatedElements = document.querySelectorAll('.fade-in-up');
            
            function checkScroll() {
                animatedElements.forEach(element => {
                    const elementPosition = element.getBoundingClientRect().top;
                    const screenPosition = window.innerHeight / 1.2;
                    
                    if (elementPosition < screenPosition) {
                        element.style.opacity = '1';
                        element.style.transform = 'translateY(0)';
                    }
                });
            }
            
            // Set initial state
            animatedElements.forEach(element => {
                element.style.opacity = '0';
                element.style.transform = 'translateY(20px)';
                element.style.transition = 'all 0.5s ease';
            });
            
            // Check on load and scroll
            window.addEventListener('load', checkScroll);
            window.addEventListener('scroll', checkScroll);
            
            // Add to cart animation
            const addButtons = document.querySelectorAll('.add-btn');
            
            addButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Prevent default only for demo purposes
                    // e.preventDefault();
                    
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i> Added';
                    this.style.backgroundColor = '#4caf50';
                    
                    setTimeout(() => {
                        this.innerHTML = originalText;
                        this.style.backgroundColor = '#ff9800';
                    }, 1000);
                    
                    // Update cart count animation
                    const cartCount = document.querySelector('.cart-count');
                    if (cartCount) {
                        cartCount.style.transform = 'scale(1.5)';
                        setTimeout(() => {
                            cartCount.style.transform = 'scale(1)';
                        }, 300);
                    }
                });
            });
        });
    </script>
</body>
</html>