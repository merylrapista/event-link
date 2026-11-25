<?php
require 'add_theme.php';

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Theme_Management</title>
        <link rel="stylesheet" href="../bootstrap/bootstrap.css">
        <style>
            body { 
                font-family: Arial; 
                display: flex;
                background: linear-gradient(to left, rgba(6,6,6,0.6), rgba(6,6,6,0.6)),
                    url('../img/hompage.jpeg') no-repeat center center;
                background-size: cover;
            }
            .sidebar {
                width: 250px; 
                background: #343a40; 
                color: white; 
                padding-top: 20px; 
                height: 100vh; 
                /* position: fixed; */
                top: 0;
                left: 0;

            }
            .sidebar h3 {
                color: white;
                padding-left: 20px;
                margin-bottom: 20px;
                font-weight: bolder;
            }
            .sidebar a { 
                display: block; 
                color: white; 
                padding: 10px 20px;
                margin-bottom: 5px;
                border-radius: 5px;
                text-decoration: none; 
            }
            .sidebar a:hover { 
                background: #495057; 
            }

          
            .nav-tabs{
                border-bottom: none;
                gap:20px;
                margin-top:15px;
            }
            .btn{
                margin-right:20px;
                margin-top:15px;

            }
            
            .nav-tabs .nav-link {
                border: none;        
                border-radius: 0;    
                color: #ffffffff;
                font-weight: 500;
                transition: all 0.2s;
            }

           
            .nav-tabs .nav-link:hover {
                background-color: rgba(45, 106, 79, 0.1);
                color: #2d6a4f;
                border: 2px solid #2d6a4f;
                border-bottom: none; 
                border-radius: 4px 4px 0 0;
            }

            .nav-tabs .nav-link.active {
                background-color: rgba(74, 169, 126, 0.15);
                color: #19e388ff;
                border: 2px solid #26c980ff;
                border-bottom: none;
                font-weight: 600;
                border-radius: 4px 4px 0 0;
            }

            .d-flex.justify-content-end .nav-tabs {
                margin-right: 10px;
            }
         
        </style>
    </head>

    <body>
        <div class="sidebar">
        <h3>Menu</h3>
        <a href="admin_dashboard.php">Dashboard</a>
        <a href="themes.php">Manage Themes</a>
        <a href="catering.php">Manage Catering</a>
        <!-- <a href="venue.php">Manage Themes</a> -->
        <a href="services.php">Manage Services</a>
        <a href="customers.php">Customers</a>
        <a href="events.php">Events</a>
        <a href="bookings.php">Bookings</a>
        <!-- <a href="staff.php">Staff</a> -->

    </div>
        <div class="container">
           <div class="navig d-flex justify-content-end align-items-center mb-3">
                <ul class="nav nav-tabs mb-0">
                    <li class="nav-item"><a class="nav-link <?= !isset($_GET['category']) ? 'active' : '' ?>" href="themes.php">All</a></li>
                    <li class="nav-item"><a class="nav-link <?= (isset($_GET['category']) && $_GET['category']=='Wedding') ? 'active' : '' ?>" href="themes.php?category=Wedding">Wedding</a></li>
                    <li class="nav-item"><a class="nav-link <?= (isset($_GET['category']) && $_GET['category']=='Birthday') ? 'active' : '' ?>" href="themes.php?category=Birthday">Birthday</a></li>
                    <li class="nav-item"><a class="nav-link <?= (isset($_GET['category']) && $_GET['category']=='Corporate') ? 'active' : '' ?>" href="themes.php?category=Corporate">Corporate</a></li>
                </ul>

                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addThemeModal">
                    ➕ Add Theme
                </button>
            </div>
        
           <?php foreach ($data as $theme_id => $themeImages): 
                $theme = $themeImages[0];
                if (isset($_GET['category']) && $_GET['category'] != $theme['category']) continue;
            ?>
            <div class="card mb-3 p-3">
                <h4><?= htmlspecialchars($theme['theme_name']) ?> (<?= htmlspecialchars($theme['category']) ?>)</h4>
                <p><?= htmlspecialchars($theme['description']) ?> | ₱<?= htmlspecialchars($theme['price']) ?></p>
                <div style="display:flex; flex-wrap:wrap;">
                    <?php foreach ($themeImages as $img): 
                        if (!empty($img['image_path'])): ?>
                            <img src="../uploads/<?= htmlspecialchars($img['image_path']) ?>" width="120" style="margin:5px; border-radius:5px;">
                    <?php endif; endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>

            <div class="modal fade" id="addThemeModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <form method="POST" action="add_theme.php" enctype="multipart/form-data">
                            <div class="modal-header">
                                <h5 class="modal-title">Add Theme</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Theme Name</label>
                                <input type="text" class="form-control" name="theme_name" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Category</label>
                                <select name="category" class="form-control">
                                    <option value="Wedding">Wedding</option>
                                    <option value="Birthday">Birthday</option>
                                    <option value="Corporate">Corporate</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" name="description" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Price</label>
                                <div class="input-group">
                                    <span class="input-group-text">₱</span>
                                    <input type="number" class="form-control" name="price" min="200" placeholder="200" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Upload Images</label>
                                <input type="file" class="form-control" name="images[]" accept="image/*" multiple>
                            </div>
                            </div>
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-primary">Save Theme</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>


        </div>
        <script src="../bootstrap/bootstrap.bundle.min.js"></script>

        
    </body>
</html>