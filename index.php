<?php
session_start();

// Cấu hình
require_once 'config/database.php';
require_once 'config/config.php';

// Helper functions
require_once 'helpers/functions.php';

// Controllers
require_once 'controllers/HomeController.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/MenuController.php';
require_once 'controllers/CartController.php';
require_once 'controllers/OrderController.php';
require_once 'controllers/ProfileController.php';
require_once 'controllers/PostController.php';
require_once 'controllers/FavoritesController.php';
require_once 'controllers/CommentController.php';

// Admin Controllers
require_once 'controllers/AdminController.php';
require_once 'controllers/AdminUserController.php';
require_once 'controllers/AdminProductController.php';
require_once 'controllers/AdminOrderController.php';
require_once 'controllers/AdminPostController.php';
require_once 'controllers/AdminRevenueController.php';

// Routing
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? 'index';

switch ($page) {
    case 'home':
        $controller = new HomeController();
        $controller->index();
        break;
        
    case 'login':
        $controller = new AuthController();
        if ($action === 'submit') {
            $controller->login();
        } else {
            $controller->showLogin();
        }
        break;
        
    case 'register':
        $controller = new AuthController();
        if ($action === 'submit') {
            $controller->register();
        } else {
            $controller->showRegister();
        }
        break;
        
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
        
    case 'profile':
        $controller = new ProfileController();
        if ($action === 'update') {
            $controller->update();
        } else {
            $controller->index();
        }
        break;
        
    case 'menu':
        $controller = new MenuController();
        if ($action === 'search') {
            $controller->search();
        } elseif ($action === 'detail') {
            $controller->detail();
        } else {
            $controller->index();
        }
        break;

    case 'posts':
        $controller = new PostController();
        $controller->index();
        break;

    case 'post':
        $controller = new PostController();
        $controller->show();
        break;
        
    case 'favorites':
        $controller = new FavoritesController();
        switch ($action) {
            case 'add':
                $controller->add();
                break;
            case 'remove':
                $controller->remove();
                break;
            case 'toggle':
                $controller->toggle();
                break;
            default:
                $controller->index();
        }
        break;
        
    case 'cart':
        $controller = new CartController();
        switch ($action) {
            case 'add':
                $controller->add();
                break;
            case 'update':
                $controller->update();
                break;
            case 'remove':
                $controller->remove();
                break;
            case 'checkout':
                $controller->checkout();
                break;
            case 'updateAddress':
                $controller->updateAddress();
                break;
            case 'placeOrder':
                $controller->placeOrder();
                break;
            case 'success':
                $controller->success();
                break;
            default:
                $controller->index();
        }
        break;
        
    case 'comments':
        $controller = new CommentController();
        switch ($action) {
            case 'form':
                $controller->showOrderCommentForm();
                break;
            case 'submit':
                $controller->submitOrderComment();
                break;
            case 'order_review':
                $controller->showOrderReview();
                break;
            case 'submit_order_reviews':
                $controller->submitOrderReviews();
                break;
            default:
                redirect('index.php');
        }
        break;
        
    case 'orders':
        $controller = new OrderController();
        if ($action === 'detail') {
            $controller->detail();
        } else {
            $controller->index();
        }
        break;
        
    // Admin Routes
    case 'admin':
        $section = $_GET['section'] ?? 'dashboard';
        
        switch ($section) {
            case 'dashboard':
                $controller = new AdminController();
                $controller->dashboard();
                break;
                
            case 'users':
                $controller = new AdminUserController();
                switch ($action) {
                    case 'update_status':
                        $controller->updateStatus();
                        break;
                    case 'delete':
                        $controller->delete();
                        break;
                    default:
                        $controller->index();
                }
                break;
                
            case 'products':
                $controller = new AdminProductController();
                switch ($action) {
                    case 'create':
                        $controller->create();
                        break;
                    case 'store':
                        $controller->store();
                        break;
                    case 'edit':
                        $controller->edit();
                        break;
                    case 'update':
                        $controller->update();
                        break;
                    case 'delete':
                        $controller->delete();
                        break;
                    default:
                        $controller->index();
                }
                break;
                
            case 'orders':
                $controller = new AdminOrderController();
                switch ($action) {
                    case 'detail':
                        $controller->detail();
                        break;
                    case 'update_status':
                        $controller->updateStatus();
                        break;
                    case 'update_payment_status':
                        $controller->updatePaymentStatus();
                        break;
                    default:
                        $controller->index();
                }
                break;
                
            case 'posts':
                $controller = new AdminPostController();
                switch ($action) {
                    case 'create':
                        $controller->create();
                        break;
                    case 'store':
                        $controller->store();
                        break;
                    case 'edit':
                        $controller->edit();
                        break;
                    case 'update':
                        $controller->update();
                        break;
                    case 'delete':
                        $controller->delete();
                        break;
                    default:
                        $controller->index();
                }
                break;
                
            case 'revenue':
                $controller = new AdminRevenueController();
                $controller->index();
                break;
                
            default:
                $controller = new AdminController();
                $controller->dashboard();
        }
        break;
        
    default:
        http_response_code(404);
        echo "Trang không tồn tại!";
        break;
}
