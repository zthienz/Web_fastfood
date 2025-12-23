<?php

class PostController {
    public function show() {
        $id = $_GET['id'] ?? 1;

        // Sample data for an article (fallback if no DB data)
        $post = [
            'id' => $id,
            'title' => 'Bánh Burger Bò Thượng Hạng',
            'image' => 'public/images/products/burger-bo-1.jpg',
            'price' => 59000,
            'description' => "Burger bò thơm ngon, thịt bò tươi, sốt đặc biệt và rau củ tươi. Phù hợp cho bữa ăn nhanh, bổ dưỡng và ngon miệng."
        ];

        require_once 'views/posts/article.php';
    }
}
