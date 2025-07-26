<?php 
    use yii\helpers\Url;
?>

<aside class="main-sidebar">

    <section class="sidebar">

        <!-- Sidebar user panel -->
        <?php if (!Yii::$app->user->isGuest) { ?>
        <div class="user-panel">
            <div class="pull-left image">
                <img src="<?= Url::base().'/img/user.jpg' ?>" class="img-circle" alt="User Image"/>
            </div>
            <div class="pull-left info">
                <p><?= Yii::$app->user->identity->username ?></p>

                <a href="#"><i class="circle text-success"></i> <?= Yii::$app->user->identity->email ?></a>
            </div>
        </div>
        <?php } ?>

        <?php   
            $menuItems = [
                ['label' => '<b>MENU</b>', 'encode' => false, 'options' => ['class' => 'header']],
                ['label' => 'Login', 'url' => ['site/login'], 'visible' => Yii::$app->user->isGuest],

                ['label' => 'Dashboard', 'icon' => 'dashboard', 'url' => ['/site/index']],
                [
                    'label' => 'Data Setting',
                    'icon' => 'gear',
                    'url' => '#',
                    'options' => ['class' => 'treeview'],
                        // 'visible' => Yii::$app->user->can('admin'),
                    'items' => [
                        ['label' => 'Barang', 'url' => ['/item/index']],
                        ['label' => 'Pelanggan', 'url' => ['/customer/index']],
                        ['label' => 'Distributor', 'url' => ['/supplier/index']],
                        ['label' => 'Salesman', 'url' => ['/salesman/index']],
                        ['label' => 'Kelompok Harga', 'url' => ['/price-group/index']],
                        // ['label' => 'System Configuration', 'url' => ['/config/index']],
                        // ['label' => 'Unit of Measurement', 'url' => ['/unit-of-measurement/index']],
                    ],
                ],
                // ['label' => 'Pelanggan', 'icon' => 'street-view', 'url' => ['/customer/index']],
                // ['label' => 'Barang', 'icon' => 'motorcycle', 'url' => ['/item/index']],
                ['label' => 'Order', 'icon' => 'tags', 'url' => ['/order/index']],
                ['label' => 'Pembelian', 'icon' => 'cube', 'url' => ['/incoming-purchase/index']],
                // ['label' => 'Pembelian - Item', 'icon' => 'cube', 'url' => ['/incoming-item/index']],
                ['label' => 'Penjualan', 'icon' => 'cubes', 'url' => ['/outgoing-sale/index']],
                // ['label' => 'Penjualan - Item', 'icon' => 'cubes', 'url' => ['/outgoing-item/index']],
                ['label' => 'Pembayaran', 'icon' => 'money', 'url' => ['/payment/index']],
                // ['label' => 'Pembayaran', 'icon' => 'money', 'url' => ['/payment/index', 'type' => 'out']],
                // ['label' => 'Penerimaan', 'icon' => 'money', 'url' => ['/payment/index', 'type' => 'in']],
                ['label' => 'Pengeluaran', 'icon' => 'leaf', 'url' => ['/expense/index']],
                ['label' => 'Pemasukan', 'icon' => 'envelope-open', 'url' => ['/capital/index']],
                [
                    'label' => 'Laporan',
                    'icon' => 'files-o',
                    'url' => '#',
                    'options' => ['class' => 'treeview'],
                    'items' => [
                        ['label' => 'Pembelian',            'url' => ['/report/incoming']],
                        // ['label' => 'Pembelian - Item',     'url' => ['/report/incoming-item']],
                        ['label' => 'Penjualan',            'url' => ['/report/outgoing']],
                        // ['label' => 'Penjualan - Item',     'url' => ['/report/outgoing-item']],
                        // ['label' => 'Pembayaran',           'url' => ['/report/payment']],
                        ['label' => 'Pembayaran Masuk',     'url' => ['/report/payment-in']],
                        ['label' => 'Pembayaran Keluar',    'url' => ['/report/payment-out']],
                        ['label' => 'Pengeluaran',          'url' => ['/report/expense'],      'visible' => Yii::$app->user->can('owner')],
                        ['label' => 'Piutang',              'url' => ['/report/debt']],
                        // ['label' => 'History Piutang',      'url' => ['/report/debt-history']],
                        ['label' => 'Resume',               'url' => ['/report/balance']],
                        // ['label' => 'History Stok',         'url' => ['/report/stock-history']],
                    ],
                ],
                
                ['label' => 'User', 'icon' => 'user', 'url' => ['/user/index'], /* 'visible' => Yii::$app->user->can('superuser') */],
                /* [
                    'label' => 'Access Control',
                    'icon' => 'lock',
                    'url' => '#',
                    'options' => ['class' => 'treeview'],
                    'visible' => Yii::$app->user->can('superuser'),
                    'items' => [
                        ['label' => 'User',         'url' => ['/user/index']],
                        ['label' => 'Assignment',   'url' => ['/acf/assignment']],
                        ['label' => 'Role',         'url' => ['/acf/role']],
                        ['label' => 'Permission',   'url' => ['/acf/permission']],
                        ['label' => 'Route',        'url' => ['/acf/route']],
                        ['label' => 'Rule',         'url' => ['/acf/rule']],
                    ],
                ], */
                // ['label' => 'Log', 'icon' => 'clock-o', 'url' => ['/log/index'],'visible' => Yii::$app->user->can('superuser')],
            ];

            $menuItems = mdm\admin\components\Helper::filter($menuItems);
        ?>

        <?= dmstr\widgets\Menu::widget(
            [
                'options' => ['class' => 'sidebar-menu tree', 'data-widget'=> 'tree'],
                'items' => $menuItems,
            ]
        ) ?>
        
        <!-- <ul class="sidebar-menu"><li><a href="<?= \yii\helpers\Url::to(['site/logout']) ?>" data-method="post"><i class="sign-out"></i>  <span>Logout</span></a></li></ul> -->
    
    </section>

</aside>
