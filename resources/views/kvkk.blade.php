@extends('layouts.app')
@section('title', 'KVKK Aydınlatma Metni — Filmincele')

@section('content')
<div class="max-w-3xl mx-auto px-4 py-12">
    <h1 class="text-3xl font-bold text-white mb-8">KVKK Aydınlatma Metni</h1>

    <div class="space-y-8 text-gray-300 leading-relaxed text-sm">

        <div>
            <h2 class="text-xl font-semibold text-white mb-3">1. Veri Sorumlusu</h2>
            <p>6698 sayılı Kişisel Verilerin Korunması Kanunu ("KVKK") kapsamında, filmincele.com olarak kişisel verileriniz veri sorumlusu sıfatıyla tarafımızca işlenmektedir.</p>
            <p class="mt-2">Veri Sorumlusu: Filmincele<br>E-posta: info@filmincele.com</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-white mb-3">2. İşlenen Kişisel Veriler</h2>
            <ul class="list-disc pl-5 space-y-1">
                <li>Ad soyad</li>
                <li>E-posta adresi</li>
                <li>Profil fotoğrafı (Google/GitHub ile girişte)</li>
                <li>Film puanlamaları ve yorumları</li>
                <li>İzleme listeleri ve favori tür/oyuncu tercihleri</li>
                <li>IP adresi ve site kullanım verileri</li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-white mb-3">3. İşleme Amaçları</h2>
            <ul class="list-disc pl-5 space-y-1">
                <li>Üyelik hesabının oluşturulması ve yönetimi</li>
                <li>Kişiselleştirilmiş film önerileri sunulması</li>
                <li>Puanlama ve liste özelliklerinin kullanılabilmesi</li>
                <li>Site istatistiklerinin tutulması ve hizmet kalitesinin artırılması</li>
                <li>Yasal yükümlülüklerin yerine getirilmesi</li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-white mb-3">4. Veri Toplama Yöntemi</h2>
            <p>Kişisel verileriniz, filmincele.com web sitesi üzerinden üyelik kaydı sırasında ve site kullanımınız esnasında otomatik yollarla toplanmaktadır. Google ve GitHub gibi üçüncü taraf hizmetler üzerinden giriş yapmanız halinde, bu platformlardaki genel profil bilgileriniz (ad, e-posta, profil fotoğrafı) KVKK'ya uygun olarak alınmaktadır.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-white mb-3">5. Verilerin Aktarımı</h2>
            <p>Kişisel verileriniz, yurt içinde veya yurt dışında herhangi bir üçüncü kişi veya kuruluşla paylaşılmamaktadır. Yalnızca yetkili kamu kurumlarının talebi halinde yasal yükümlülüklerimiz gereği paylaşım yapılabilir.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-white mb-3">6. Veri Güvenliği</h2>
            <p>Kişisel verilerinizin güvenliğini sağlamak için endüstri standardı güvenlik önlemleri alınmaktadır. Şifreleriniz hash'lenerek (BCrypt) saklanmakta olup, hiçbir şekilde düz metin olarak depolanmamaktadır.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-white mb-3">7. Haklarınız (KVKK Madde 11)</h2>
            <p>KVKK'nın 11. maddesi kapsamında aşağıdaki haklara sahipsiniz:</p>
            <ul class="list-disc pl-5 space-y-1 mt-2">
                <li>Kişisel verilerinizin işlenip işlenmediğini öğrenme</li>
                <li>İşlenmişse buna ilişkin bilgi talep etme</li>
                <li>İşlenme amacını ve amacına uygun kullanılıp kullanılmadığını öğrenme</li>
                <li>Yurt içinde / yurt dışında aktarıldığı üçüncü kişileri bilme</li>
                <li>Eksik veya yanlış işlenmişse düzeltilmesini isteme</li>
                <li>KVKK 7. maddede öngörülen şartlar çerçevesinde silinmesini isteme</li>
                <li>İşlenen verilerin münhasıran otomatik sistemler ile analiz edilmesi suretiyle aleyhinize bir sonuç ortaya çıkmasına itiraz etme</li>
                <li>Kanuna aykırı olarak işlenmesi sebebiyle zarara uğramanız halinde zararın giderilmesini talep etme</li>
            </ul>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-white mb-3">8. İletişim</h2>
            <p>KVKK kapsamındaki haklarınızla ilgili taleplerinizi info@filmincele.com adresine e-posta göndererek iletebilirsiniz. Talebiniz en geç 30 gün içinde değerlendirilip sonuçlandırılacaktır.</p>
        </div>

        <div>
            <h2 class="text-xl font-semibold text-white mb-3">9. Çerezler</h2>
            <p>Sitemiz, hizmet kalitesini artırmak ve oturum yönetimi sağlamak amacıyla çerezler kullanmaktadır. Tarayıcı ayarlarınızdan çerezleri devre dışı bırakabilirsiniz.</p>
        </div>

        <p class="text-gray-500 text-xs pt-4 border-t border-gray-800">Son güncelleme: {{ date('d.m.Y') }}</p>
    </div>
</div>
@endsection
