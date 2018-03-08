<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\nhanvien_donvay;
use App\member;
use App\comment;
use App\shophoso;
use App\User;
use App\hoso;
use App\phonggiaodich;
use App\chucvu;
use App\nhanvien_pgd;
use Illuminate\Support\Facades\Auth;
use App\fileupload;
use App\trangthaihoso;
use Image;
use Input,File;
use App\Http\Requests\thayavatar;
use App\thongtinkhachhang;
use Illuminate\Database\Eloquent\Model;


class MyController extends Controller
{
    //
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function simple()
    {
    	return view('simple');
    }
    public function index()
    {
    	return view('index',['menu'=>'index']);
    }
    public function chart()
    {
    	return view('morris');
    }
    public function demomember()
    {
    	return view('demomember',['menu'=>'demomember']);
    }
    public function thanhvien()
    {
        $user = DB::table('users')->select('id','name','email','rule','hoten','sdt','avatar','gioitinh','phong')->get();
        $chucvu = DB::table('chucvu')->get();
        $phonggiaodich = DB::table('phonggiaodich')->get();
        return view('thanhvien',['user'=>$user,'chucvu'=>$chucvu,'menu'=>'thanhvien','phonggiaodich'=>$phonggiaodich]);
    }
    public function donxinvay()
    {
        $phong = Auth::user()->phong;
        if (Auth::user()->rule == 1 || Auth::user()->rule == 2) {
            $hoso = DB::table('hoso')->get();
            $ttkh = DB::table('thongtinkhachhang')->select('id','idmember')->get();
            $member = DB::table('member')->select('id','hoten','sdt','cmt')->get();
        }elseif (Auth::user()->rule == 3 || Auth::user()->rule == 4) {
            $hoso = DB::table('hoso')->where('pgd',$phong)->get();
            $ttkh = DB::table('thongtinkhachhang')->select('id','idmember')->get();
            $member = DB::table('member')->select('id','hoten','sdt','cmt')->get();
        }elseif (Auth::user()->rule == 5) {
            $hoso = DB::table('hoso')->where('trangthaihopdong',4)->where('pgd',$phong)->get();
            $ttkh = DB::table('thongtinkhachhang')->select('id','idmember')->get();
            $member = DB::table('member')->select('id','hoten','sdt','cmt')->get();
        }elseif (Auth::user()->rule == 6) {
            $hoso = DB::table('hoso')->where('trangthaihopdong',1)->where('pgd',$phong)->get();
            $nhanvien_donvay = DB::table('nhanvien_donvay')->get();
            $ttkh = DB::table('thongtinkhachhang')->select('id','idmember')->get();
            $member = DB::table('member')->select('id','hoten','sdt','cmt')->get();
            $trangthaihoso= DB::table('trangthaihoso')->get();
            return view('donxinvay',['member'=>$member,'hoso'=>$hoso,'ttkh'=>$ttkh,'trangthaihoso'=>$trangthaihoso,'menu'=>'donxinvay','nhanvien_donvay'=>$nhanvien_donvay]);
        }
        $trangthaihoso= DB::table('trangthaihoso')->get();
        return view('donxinvay',['member'=>$member,'hoso'=>$hoso,'ttkh'=>$ttkh,'trangthaihoso'=>$trangthaihoso,'menu'=>'donxinvay']);
    }
    public function hoadon($id)
    {
        $idcomment='pos'.$id;
        $users = DB::table('users')->select('hoten','id','avatar','sdt')->get();
        $hoso = DB::table('hoso')->where('id',$id)->get();
        foreach ($hoso as $key) {
            $idmember = $key->idmember;
            $pgd = $key->pgd;
        }
        $checkmem = DB::table('users')->select('hoten','id','avatar')->where('phong',$pgd)->get();
        $nhanvien_donvay = DB::table('nhanvien_donvay')->where('idhoso',$id)->get();
        if (Auth::user()->rule == 3 || Auth::user()->rule == 4) {
            if (Auth::user()->phong != $pgd) {
                return redirect()->route('donxinvay');
            }
        }
        if (Auth::user()->rule == 5 || Auth::user()->rule == 6) {
            $checkuser = DB::table('nhanvien_donvay')->where('idnhanvien',Auth::user()->id)->where('idhoso',$id)->get();
            if (Auth::user()->phong != $pgd || count($checkuser) == 0) {
                return redirect()->route('donxinvay');
            }
        }
        if (Auth::user()->rule == 7) {
            return redirect()->route('tatcadonvay');
        }
        $loaivay = DB::table('loaivay')->get();
        $trangthaihoso = DB::table('trangthaihoso')->get();
        $thongtinkhachhang = DB::table('thongtinkhachhang')->where('idmember',$idmember)->get();
        $member = DB::table('member')->select('id','hoten','sdt','cmt')->where('id',$idmember)->get();
        $comment = DB::table('comment')->where('idpost',$idcomment)->get();
        $fileupload = DB::table('fileupload')->where('idhoso',$id)->get();
        return view('hoadon',['member'=>$member,'hoso'=>$hoso,'trangthaihoso'=>$trangthaihoso,'loaivay'=>$loaivay,'thongtinkhachhang'=>$thongtinkhachhang,'comment'=>$comment,'users'=>$users,'fileupload'=>$fileupload,'nhanvien_donvay'=>$nhanvien_donvay,'checkmem'=>$checkmem,'checkid'=>$id]);
    }
    public function edithoso(Request $request)
    {

        $id = $request['idhoso'];
        $sotienvay = $request['sotienvay'];
        $loaivay = $request['loaivay'];
        $sotienphaitra = $request['sotienphaitra'];
        $laimoingay = $request['laimoingay'];
        $songay = $request['songay'];
        $trangthaihopdong = $request['trangthaihopdong'];
        hoso::where('id', $id)
            ->update([
            'sotienvay' => $sotienvay,
            'sotienphaitra' => $sotienphaitra,
            'laimoingay' => $laimoingay,
            'loaivay' => $loaivay,
            'songay' => $songay,
            'trangthaihopdong' => $trangthaihopdong,
        ]);
        return redirect()->back()->with('message', 'Chỉnh sửa hợp đồng thành công.');
    }
    public function editthongtin(Request $request)
    {
        $id = $request['idthongtinkh'];
        $hoten = $request['hoten'];
        $cmt = $request['cmt'];
        $ngaysinh = $request['ngaysinh'];
        $ngaycap = $request['ngaycap'];
        $gioitinh = $request['gioitinh'];
        $email = $request['email'];
        $loaidienthoai = $request['loaidienthoai'];
        $quanhenguoithan = $request['quanhenguoithan'];
        $luongtb = $request['luongtb'];
        $hopdong = $request['hopdong'];
        $mathenh = $request['mathenh'];
        $nghenghiep = $request['nghenghiep'];
        $sdtnoilam = $request['sdtnoilam'];
        $loaithanhtoan = $request['loaithanhtoan'];
        $tennganhang = $request['tennganhang'];
        $chinhanh = $request['chinhanh'];
        member::where('id', $id)
            ->update([
            'hoten' => $hoten,
            'cmt' => $cmt,
        ]);
        thongtinkhachhang::where('idmember', $id)
            ->update([
            'ngaysinh' => $ngaysinh,
            'ngaycap' => $ngaycap,
            'gioitinh' => $gioitinh,
            'email' => $email,
            'loaidienthoai' => $loaidienthoai,
            'quanhenguoithan' => $quanhenguoithan,
            'luongtb' => $luongtb,
            'hopdong' => $hopdong,
            'mathenh' => $mathenh,
            'nghenghiep' => $nghenghiep,
            'sdtnoilam' => $sdtnoilam,
            'loaithanhtoan' => $loaithanhtoan,
            'tennganhang' => $tennganhang,
            'chinhanh' => $chinhanh,
        ]);
        return redirect()->back()->with('message2', 'Chỉnh sửa hợp đồng thành công.');
    }
    public function editmember($id)
    {
        $user = DB::table('users')->select('id','name','email','rule','hoten','sdt','avatar','gioitinh')->where('id',$id)->get();
        $chucvu = DB::table('chucvu')->get();
        return view('editmember',['id'=>$id,'user'=>$user,'chucvu'=>$chucvu,'menu'=>'thanhvien']);
    }
    public function posteditmember(Request $request)
    {
        $id = $request['id'];
        $hoten = $request['hoten'];
        $sdt = $request['sdt'];
        $email = $request['email'];
        $gioitinh = $request['gioitinh'];
        $chucvu = $request['chucvu'];
        user::where('id', $id)
            ->update([
            'hoten' => $hoten,
            'sdt' => $sdt,
            'email' => $email,
            'gioitinh' => $gioitinh,
            'rule' => $chucvu,
        ]);
        return redirect()->back()->with('message', 'Chỉnh sửa thông tin thành công.');
    }
    public function themthanhvien()
    {
        $chucvu = DB::table('chucvu')->get();
        return view('themthanhvien',['chucvu'=>$chucvu]);
    }
    public function postthemthanhvien(Request $request)
    {
        $username = $request['username'];
        $password = $request['password'];
        $hoten = $request['hoten'];
        $sdt = $request['sdt'];
        $email = $request['email'];
        $gioitinh = $request['gioitinh'];
        $chucvu = $request['chucvu'];
        DB::table('users')->insert([
            'name' => $username,
            'password' => bcrypt('123456'),
            'hoten' => $hoten,
            'sdt' => $sdt,
            'email' => $email,
            'gioitinh' => $gioitinh,
            'rule' => $chucvu,
            'avatar' => 'user2-160x160.jpg',
        ]);
        return redirect()->back()->with('message', 'Thêm tài khoản '.$username.' thành công.');
    }
    public function khachhang()
    {
        $member = DB::table('member')->select('id','hoten','sdt','cmt')->get();
        return view('khachhang',['member'=>$member,'menu'=>'khachhang']);
    }
    public function chitietkhachhang($id)
    {
        $member = DB::table('member')->select('id','hoten','sdt','cmt')->where('id',$id)->get();
        $thongtinkhachhang = DB::table('thongtinkhachhang')->where('idmember',$id)->get();
        $hoso = DB::table('hoso')->get();
        return view('chitietkhachhang',['member'=>$member,'thongtinkhachhang'=>$thongtinkhachhang,'hoso'=>$hoso,'menu'=>'khachhang']);
    }
    public function profile($name)
    {
        $user = DB::table('users')->select('id','name','email','rule','hoten','sdt','avatar','gioitinh','diachi','mota')->where('name',$name)->get();
        foreach ($user as $key) {
            $id=$key->id;
        }
        $idcomment='mem'.$id;
        $comment = DB::table('comment')->where('idpost',$idcomment)->get();
        $users = DB::table('users')->select('hoten','id','avatar')->get();

        $chucvu = DB::table('chucvu')->get();
        return view('profile',['user'=>$user,'chucvu'=>$chucvu,'comment'=>$comment,'users'=>$users,'menu'=>'profile']);
    }
    public function thayavatar(thayavatar $request)
    {
        $id = $request->id;
        if ($request->hasFile('myfile')) {
            $file = $request->file('myfile');
            $link = DB::table('users')->select('avatar')->where('id',$id)->get();
            foreach ($link as $key) {
                $a = $key->avatar;
            }
            if ($a != 'user2-160x160.jpg') {
                File::delete('public/avatar/'.$a);
            }
            $fileName = $file -> getClientOriginalName('myfile');
            $fileName = str_slug($fileName, '-');
            $fileName = $fileName.'.'.$file -> getClientOriginalExtension('myfile');
            $t=time();
            $a =  $t.'_'.$fileName;
            $file->move('public/avatar',$a);
            $doiten = 'public/avatar/'.$a;
            $img = Image::make($doiten)->resize(300, 300)->save($doiten);
            user::where('id', $id)
                ->update([
                'avatar' => $a,
            ]);

            return redirect()->back()->with('message', 'Chỉnh sửa avatar thành công.');
        }else{
            return redirect()->back();
        }
    }
    public function thaythongtincanhan(Request $request)
    {
        $hoten = $request['hoten'];
        $sdt = $request['sdt'];
        $diachi = $request['diachi'];
        $motangan = $request['motangan'];
        $id = $request['id'];
        user::where('id', $id)
            ->update([
            'hoten' => $hoten,
            'sdt' => $sdt,
            'diachi' => $diachi,
            'mota' => $motangan,
        ]);
        return redirect()->back()->with('message', 'Chỉnh sửa thông tin thành công.');
    }
    public function doimatkhau(Request $request)
    {
        $mk1 = $request['password1'];
        $id = $request['id'];
        user::where('id', $id)
            ->update([
            'password' => bcrypt($mk1)
        ]);
        return redirect()->back()->with('message', 'Đổi mật khẩu thành công.');
    }
    public function deletefile($id)
    {
        $fileupload = DB::table('fileupload')->where('id',$id)->SELECT('link')->get();
        foreach ($fileupload as $key) {
            $tenfilecu =  $key->link;
            File::delete('public/file/'.$tenfilecu);
        }
        fileupload::where('id', '=', $id)->delete();
        return redirect()->back()->with('message', 'Xóa file thành công.');
    }
    public function uploadfile(Request $request)
    {
        $id=$request['idhoso'];
        $fileupload = DB::table('fileupload')->where('idhoso',$id)->count();
        if ($fileupload >= 5) {
            return redirect()->back()->with('loifile', 'Hồ sơ này đã đạt giới hạn 5 File đính kèm.');
            # code...
        }
        if($request->hasFile('myfile')){
            $file = $request->myfile;
            if ($file->getSize() > 5000000) {
                return redirect()->back()->with('loifile', 'File dung lượng không được vượt quá 5mb.');
            }
            $t= time();
            $linkcai = $t.'-'.$file->getClientOriginalName();
            $namefile = $file->getClientOriginalName();
            // $linkcai = str_slug($linkcai, "-");
            $file->move('public/file',$linkcai); 
            $fileupload = new fileupload(); 
            $fileupload->idhoso = $id;
            $fileupload->name = $namefile;
            $fileupload->link = $linkcai;
            $fileupload->save();   
            return redirect()->back()->with('message', 'Thêm file đính kèm thành công.');
        }
            return redirect()->back();
    }
    public function phonggiaodich()
    {
        if (Auth::user()->rule >2) {
            $phong = Auth::user()->phong;
            $phonggiaodich = DB::table('phonggiaodich')->where('id',$phong)->get();
        }else{
            $phonggiaodich = DB::table('phonggiaodich')->get();
        }

        $user = DB::table('users')->select('id','name','email','hoten','rule')->get();
        return view('phonggiaodich',['phonggiaodich'=>$phonggiaodich,'user'=>$user,'menu'=>'phonggiaodich']);
    }
    public function addpgd(Request $request)
    {
        $name = $request['tenpgd'];
        $giamdoc = $request['giamdoc'];
        $diachi = $request['diachi'];
        $phonggiaodich = new phonggiaodich(); 
        $phonggiaodich->name = $name;
        $phonggiaodich->giamdoc = $giamdoc;
        $phonggiaodich->diachi = $diachi;
        $phonggiaodich->save();   
        user::where('id', $giamdoc)
            ->update([
            'rule' => '4',
        ]);
        return redirect()->back()->with('message', 'Thêm phòng giao dịch thành công.');
    }
    public function editpgd(Request $request)
    {
        $id = $request['id'];
        $name = $request['tenpgd'];
        $giamdoc = $request['giamdoc'];
        $diachi = $request['diachi'];
        phonggiaodich::where('id', $id)
            ->update([
            'name' => $name,
            'giamdoc' => $giamdoc,
            'diachi' => $diachi,
        ]);
        return redirect()->back()->with('message', 'Chỉnh sửa phòng giao dịch thành công.');
    }
    public function edittrangthai(Request $request)
    {
        $idhoso =$request['idhoso'];
        $trangthai =$request['trangthai'];
        if (substr($idhoso,0,3) == 'sop') {
            shophoso::where('id', substr($idhoso,3,4))
                ->update([
                'trangthaihopdong' => $trangthai,
            ]);
        }else{
            hoso::where('id', $idhoso)
                ->update([
                'trangthaihopdong' => $trangthai,
            ]);
        }
        return redirect()->back()->with('message', 'Thay đổi trạng thái thành công.');
    }
    public function pgd($id)
    {
        $idcomment='pgd'.$id;
        $comment = DB::table('comment')->where('idpost',$idcomment)->get();
        $giamdoc = DB::table('phonggiaodich')->where('id',$id)->select('giamdoc')->get();
        $users = DB::table('users')->select('name','hoten','id','avatar','sdt','rule','phong')->get();
        foreach ($giamdoc as $key) {
            $giamdoc = $key->giamdoc;
        }
        return view('pgd',['comment'=>$comment,'users'=>$users,'idpgd'=>$id,'giamdoc'=>$giamdoc]);
    }
    public function xoaphong($id)
    {
        user::where('id', $id)
            ->update([
            'phong' => null,
        ]);
        return redirect()->back()->with('message', 'Xóa nhân viên thành công.');
    }
    public function adduserpgd(Request $request)
    {
        $idpgd = $request['idpgd'];
        $iduser = $request['iduser'];
        //gogogogogogogo
        if ($iduser == 0) {
            return redirect()->back()->with('message', 'Bạn chưa chọn nhân viên.');
        }
        user::where('id', $iduser)
            ->update([
            'phong' => $idpgd,
        ]);
        return redirect()->back()->with('message', 'Thêm nhân viên thành công.');
    }
    public function addnhanvien(Request $request)
    {
        $user = $request['user'];
        $idpgd = $request['idpgd'];
        $nhanvien_donvay = new nhanvien_donvay(); 
        $nhanvien_donvay->idnhanvien = $user;
        $nhanvien_donvay->idhoso = $idpgd;
        $nhanvien_donvay->save();
        return redirect()->back()->with('message', 'Thêm nhân viên thành công.');
    }
}
