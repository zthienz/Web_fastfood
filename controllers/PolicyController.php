<?php

class PolicyController {
    
    public function index() {
        $type = $_GET['type'] ?? 'warranty';
        
        $policies = [
            'warranty' => [
                'title' => 'Chính sách bảo hành',
                'content' => $this->getWarrantyPolicy()
            ],
            'return' => [
                'title' => 'Chính sách đổi trả',
                'content' => $this->getReturnPolicy()
            ],
            'shipping' => [
                'title' => 'Chính sách vận chuyển',
                'content' => $this->getShippingPolicy()
            ],
            'payment' => [
                'title' => 'Chính sách thanh toán',
                'content' => $this->getPaymentPolicy()
            ]
        ];
        
        if (!isset($policies[$type])) {
            $type = 'warranty';
        }
        
        $policy = $policies[$type];
        $pageTitle = $policy['title'] . ' - FastFood';
        
        include 'views/layouts/header.php';
        include 'views/policy/index.php';
        include 'views/layouts/footer.php';
    }
    
    private function getWarrantyPolicy() {
        return [
            'intro' => 'FastFood cam kết mang đến cho khách hàng những sản phẩm thức ăn nhanh chất lượng cao và dịch vụ bảo hành tốt nhất.',
            'sections' => [
                [
                    'title' => '1. Phạm vi bảo hành',
                    'content' => [
                        'Bảo hành chất lượng thực phẩm trong vòng 2 giờ kể từ khi giao hàng',
                        'Bảo hành về độ tươi ngon và an toàn thực phẩm',
                        'Bảo hành đúng món ăn như đã đặt hàng',
                        'Bảo hành nhiệt độ phù hợp khi giao hàng'
                    ]
                ],
                [
                    'title' => '2. Điều kiện bảo hành',
                    'content' => [
                        'Sản phẩm còn nguyên vẹn, chưa sử dụng',
                        'Có hóa đơn mua hàng hoặc mã đơn hàng',
                        'Thông báo trong vòng 30 phút sau khi nhận hàng nếu có vấn đề',
                        'Sản phẩm không bị ảnh hưởng bởi yếu tố bên ngoài'
                    ]
                ],
                [
                    'title' => '3. Quy trình bảo hành',
                    'content' => [
                        'Liên hệ hotline: 1900-xxxx hoặc email: support@fastfood.com',
                        'Cung cấp thông tin đơn hàng và mô tả vấn đề',
                        'Đội ngũ kỹ thuật sẽ xử lý trong vòng 15 phút',
                        'Hoàn tiền hoặc giao lại sản phẩm mới miễn phí'
                    ]
                ]
            ]
        ];
    }
    
    private function getReturnPolicy() {
        return [
            'intro' => 'Chính sách đổi trả linh hoạt nhằm đảm bảo quyền lợi tối đa cho khách hàng khi mua sắm tại FastFood.',
            'sections' => [
                [
                    'title' => '1. Điều kiện đổi trả',
                    'content' => [
                        'Sản phẩm chưa sử dụng, còn nguyên vẹn',
                        'Thời gian đổi trả trong vòng 30 phút sau khi nhận hàng',
                        'Có hóa đơn mua hàng hoặc mã đơn hàng hợp lệ',
                        'Sản phẩm còn trong bao bì gốc'
                    ]
                ],
                [
                    'title' => '2. Các trường hợp được đổi trả',
                    'content' => [
                        'Sản phẩm không đúng như mô tả trên menu',
                        'Sản phẩm bị hỏng trong quá trình vận chuyển',
                        'Sản phẩm không đảm bảo chất lượng (nguội, không tươi)',
                        'Giao nhầm món ăn hoặc thiếu món'
                    ]
                ],
                [
                    'title' => '3. Quy trình đổi trả',
                    'content' => [
                        'Liên hệ bộ phận chăm sóc khách hàng ngay lập tức',
                        'Cung cấp thông tin đơn hàng và lý do đổi trả',
                        'Giữ nguyên sản phẩm theo hướng dẫn',
                        'Nhận sản phẩm mới hoặc hoàn tiền trong 1 giờ'
                    ]
                ]
            ]
        ];
    }
    
    private function getShippingPolicy() {
        return [
            'intro' => 'FastFood cung cấp dịch vụ giao hàng nhanh chóng, đảm bảo thức ăn còn nóng và tươi ngon khi đến tay khách hàng.',
            'sections' => [
                [
                    'title' => '1. Khu vực giao hàng',
                    'content' => [
                        'Giao hàng trong nội thành TP.HCM: 15-30 phút',
                        'Giao hàng ngoại thành TP.HCM: 30-45 phút',
                        'Giao hàng các quận lân cận: 45-60 phút',
                        'Phục vụ 24/7 cho các khu vực trung tâm'
                    ]
                ],
                [
                    'title' => '2. Phí vận chuyển',
                    'content' => [
                        'Miễn phí giao hàng cho đơn hàng từ 150.000đ',
                        'Phí giao hàng nội thành: 10.000đ',
                        'Phí giao hàng ngoại thành: 20.000đ',
                        'Phí giao hàng ban đêm (22h-6h): +5.000đ'
                    ]
                ],
                [
                    'title' => '3. Cam kết chất lượng giao hàng',
                    'content' => [
                        'Thức ăn được đóng gói cẩn thận, giữ nhiệt',
                        'Shipper được đào tạo chuyên nghiệp',
                        'Theo dõi đơn hàng real-time qua app',
                        'Hoàn tiền nếu giao hàng trễ quá 15 phút'
                    ]
                ]
            ]
        ];
    }
    
    private function getPaymentPolicy() {
        return [
            'intro' => 'FastFood hỗ trợ đa dạng hình thức thanh toán để mang lại sự tiện lợi tối đa cho khách hàng khi đặt thức ăn nhanh.',
            'sections' => [
                [
                    'title' => '1. Hình thức thanh toán',
                    'content' => [
                        'Thanh toán khi nhận hàng (COD)',
                        'Chuyển khoản ngân hàng',
                        'Thanh toán qua ví điện tử (MoMo, ZaloPay, ShopeePay)',
                        'Thanh toán bằng thẻ tín dụng/ghi nợ'
                    ]
                ],
                [
                    'title' => '2. Thông tin chuyển khoản',
                    'content' => [
                        'Ngân hàng: Vietcombank',
                        'Số tài khoản: 1234567890',
                        'Chủ tài khoản: CÔNG TY FASTFOOD',
                        'Nội dung: Mã đơn hàng + Số điện thoại'
                    ]
                ],
                [
                    'title' => '3. Chính sách hoàn tiền',
                    'content' => [
                        'Hoàn tiền 100% nếu hủy đơn trước khi chế biến (trong 5 phút)',
                        'Hoàn 100% nếu lỗi từ phía nhà hàng',
                        'Hoàn 50% nếu khách hàng hủy sau khi bắt đầu chế biến',
                        'Không hoàn tiền nếu khách hàng từ chối nhận hàng không lý do'
                    ]
                ]
            ]
        ];
    }
}