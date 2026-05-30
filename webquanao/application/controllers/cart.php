<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Cart extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('product_model');
        $this->load->model('cart_model');
    }

    public function index()
    {
        $user = $this->session->userdata('user');
        $message = $this->session->flashdata('message');
        $this->data['message'] = $message;

        if (isset($user)) {
            $carts = $this->cart_model->get_list(['where' => ['user_id' => $user->id]]);
        } else {
            $carts = $this->session->userdata('guest_cart');
            if (!$carts) {
                $carts = [];
            }
        }

        $formatted_cart = [];
        $total_qty = 0;

        foreach ($carts as $k => $item) {
            $product = $this->product_model->get_info($item->product_id);
            $stock = $product ? max(0, (int) $product->stock) : 0;
            $qty = (int) $item->qty;
            if ($qty > $stock) {
                $qty = $stock;
                if ($stock > 0) {
                    if (isset($user)) {
                        $this->cart_model->update_rule(
                            ['user_id' => $user->id, 'product_id' => $item->product_id],
                            ['qty' => $stock]
                        );
                    } else {
                        $carts[$k]->qty = $stock;
                    }
                }
            }
            $formatted_cart[] = [
                'id' => $item->product_id,
                'qty' => $qty,
                'price' => $item->price,
                'name' => $item->name,
                'image_link' => $item->image_link,
                'rowid' => $item->rowid,
                'stock' => $stock,
                'subtotal' => $item->price * $qty,
            ];
            $total_qty += $qty;
        }

        if (!isset($user)) {
            $this->session->set_userdata('guest_cart', $carts);
        }

        $this->data['carts'] = $formatted_cart;
        $this->data['total_items'] = isset($user) ? $this->cart_model->get_sum('qty', ['user_id' => $user->id]) : $total_qty;

        $this->data['temp'] = 'site/cart/index';
        $this->load->view('site/layoutsub', $this->data);
    }

    public function add()
    {
        $user = $this->session->userdata('user');
        
        $id = $this->input->post('id');
        if (!$id) {
            $id = $this->uri->rsegment(3);
        }
        $id = intval($id);
        $product = $this->product_model->get_product_with_discount($id);
        
        $is_ajax = $this->input->is_ajax_request() || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');

        if (!$product) {
            if ($is_ajax) {
                echo json_encode(["status" => "error", "message" => "Sản phẩm không tồn tại."], JSON_UNESCAPED_UNICODE);
                return;
            }
            redirect(base_url('/'));
            return;
        }
        if ((int) $product->stock < 1) {
            if ($is_ajax) {
                echo json_encode(["status" => "error", "message" => "Sản phẩm đã hết hàng."], JSON_UNESCAPED_UNICODE);
                return;
            }
            $this->session->set_flashdata('message', 'Sản phẩm đã hết hàng.');
            redirect(base_url());
            return;
        }
        
        $qty = 1;
        $price = $product->price;
        if ($product->discount > 0) {
            $price = $product->price - $product->discount;
        }
        $rowid = md5(mt_rand(1, 1000));

        if (isset($user)) {
            $qty_ex = $this->cart_model->get_info_rule(['user_id' => $user->id, 'product_id' => $id], 'qty');
            if ($qty_ex) {
                $new_qty = (int) $qty_ex->qty + 1;
                if ($new_qty > (int) $product->stock) {
                    if ($is_ajax) {
                        echo json_encode(["status" => "error", "message" => "Chỉ còn " . (int) $product->stock . " sản phẩm trong kho."], JSON_UNESCAPED_UNICODE);
                        return;
                    }
                    $this->session->set_flashdata('message', 'Chỉ còn ' . (int) $product->stock . ' sản phẩm trong kho.');
                    redirect(base_url('cart'));
                    return;
                }
                $this->cart_model->update_rule(['user_id' => $user->id, 'product_id' => $id], ['qty' => $new_qty]);
                if (!$is_ajax) {
                    redirect(base_url('cart'));
                    return;
                }
            } else {
                $data = array();
                $data['user_id'] = $user->id;
                $data['product_id'] = $id;
                $data['qty'] = $qty;
                $data['price'] = $price;
                $data['name'] = $product->name;
                $data['image_link'] = $product->image_link;
                $data['rowid'] = $rowid;
                
                $this->cart_model->create($data);
            }
        } else {
            $guest_cart = $this->session->userdata('guest_cart');
            if (!$guest_cart) {
                $guest_cart = [];
            }
            $found = false;
            foreach ($guest_cart as $k => $item) {
                if ($item->product_id == $id) {
                    $new_qty = (int) $item->qty + 1;
                    if ($new_qty > (int) $product->stock) {
                        if ($is_ajax) {
                            echo json_encode(["status" => "error", "message" => "Chỉ còn " . (int) $product->stock . " sản phẩm trong kho."], JSON_UNESCAPED_UNICODE);
                            return;
                        }
                        $this->session->set_flashdata('message', 'Chỉ còn ' . (int) $product->stock . ' sản phẩm trong kho.');
                        redirect(base_url('cart'));
                        return;
                    }
                    $guest_cart[$k]->qty = $new_qty;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $guest_cart[] = (object) [
                    'product_id' => $id,
                    'qty' => $qty,
                    'price' => $price,
                    'name' => $product->name,
                    'image_link' => $product->image_link,
                    'rowid' => $rowid
                ];
            }
            $this->session->set_userdata('guest_cart', $guest_cart);
        }

        if ($is_ajax) {
            $total_qty = 0;
            if (isset($user)) {
                $total_qty = $this->cart_model->get_sum('qty', ['user_id' => $user->id]);
            } else {
                $g_cart = $this->session->userdata('guest_cart');
                if ($g_cart) {
                    foreach ($g_cart as $i) $total_qty += $i->qty;
                }
            }
            echo json_encode(["status" => "success", "message" => "Sản phẩm đã được thêm vào giỏ hàng!", "cart_total" => $total_qty], JSON_UNESCAPED_UNICODE);
            return;
        }

        redirect(base_url('cart'));
    }

    public function update_ajax($id)
    {
        $user = $this->session->userdata('user');
        header('Content-Type: application/json');

        $qty = $this->input->post('qty');
        if ($qty < 1) {
            exit(json_encode([
                'status' => 'error',
                'message' => 'Số lượng sản phẩm không hợp lệ'
            ], JSON_UNESCAPED_UNICODE));
        }

        if (isset($user)) {
            $carts = $this->cart_model->get_list(['where' => ['user_id' => $user->id]]);
        } else {
            $carts = $this->session->userdata('guest_cart');
            if (!$carts) $carts = [];
        }

        foreach ($carts as $k => $value) {
            if ($value->product_id == $id) {
                $product = $this->product_model->get_info($id);
                if (!$product || (int) $qty > (int) $product->stock) {
                    $available = $product ? (int) $product->stock : 0;
                    exit(json_encode([
                        'status' => 'error',
                        'message' => 'Chỉ còn ' . $available . ' sản phẩm trong kho.',
                    ], JSON_UNESCAPED_UNICODE));
                }

                if (isset($user)) {
                    $data = array();
                    $data['qty'] = $qty;
                    $data['rowid'] = md5(mt_rand(1, 1000));
                    $this->cart_model->update_rule(['user_id' => $user->id, 'product_id' => $id], $data);
                } else {
                    $carts[$k]->qty = $qty;
                    $carts[$k]->rowid = md5(mt_rand(1, 1000));
                    $this->session->set_userdata('guest_cart', $carts);
                }

                exit(json_encode([
                    'status' => 'success',
                    'message' => 'Cập nhật giỏ hàng thành công!',
                    'max_stock' => (int) $product->stock,
                ], JSON_UNESCAPED_UNICODE));
            }
        }

        exit(json_encode(array('status' => 'error', 'message' => 'Không tìm thấy sản phẩm trong giỏ hàng'), JSON_UNESCAPED_UNICODE));
    }

    public function del()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            header('Content-Type: application/json;');

            $user = $this->session->userdata('user');

            $data = json_decode(file_get_contents("php://input"), true);
            if (!$data) {
                echo json_encode(["status" => "error", "message" => "Không nhận được dữ liệu"], JSON_UNESCAPED_UNICODE);
                return;
            }

            $id = intval($data['id']);

            if (isset($user)) {
                $carts = $this->cart_model->get_list(['where' => ['user_id' => $user->id]]);
                if ($id > 0) {
                    foreach ($carts as $key => $value) {
                        if ($value->product_id == $id) {
                            $this->cart_model->del_rule(['user_id' => $user->id, 'product_id' => $id]);
                            echo json_encode(["status" => "success", "message" => "Xóa sản phẩm thành công"], JSON_UNESCAPED_UNICODE);
                            return;
                        }
                    }
                } else {
                    $this->cart_model->del_rule(['user_id' => $user->id]);
                    echo json_encode(["status" => "success", "message" => "Xóa giỏ hàng thành công"], JSON_UNESCAPED_UNICODE);
                    return;
                }
            } else {
                $guest_cart = $this->session->userdata('guest_cart');
                if (!$guest_cart) $guest_cart = [];

                if ($id > 0) {
                    foreach ($guest_cart as $key => $value) {
                        if ($value->product_id == $id) {
                            unset($guest_cart[$key]);
                            $this->session->set_userdata('guest_cart', array_values($guest_cart));
                            echo json_encode(["status" => "success", "message" => "Xóa sản phẩm thành công"], JSON_UNESCAPED_UNICODE);
                            return;
                        }
                    }
                } else {
                    $this->session->unset_userdata('guest_cart');
                    echo json_encode(["status" => "success", "message" => "Xóa giỏ hàng thành công"], JSON_UNESCAPED_UNICODE);
                    return;
                }
            }
        }
    }
}
