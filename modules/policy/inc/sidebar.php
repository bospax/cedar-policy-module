<aside class="left-sidebar">
    <!-- Sidebar scroll-->
    <div class="scroll-sidebar">
        <!-- Sidebar navigation-->
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <!-- User Profile-->
                <?php if (in_array('policy', $module_permissions) && in_array('policy-dashboard', $logged_userpermissions)) : ?>
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="index.php?route=policy" aria-expanded="false"><i class="mdi mdi-widgets"></i><span class="hide-menu">Dashboard</span></a></li>
                <?php endif; ?>

                <?php if (in_array('policy', $module_permissions) && in_array('policy-browse', $logged_userpermissions)) : ?>
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="index.php?route=policy/browse" aria-expanded="false"><i class="mdi mdi-search-web"></i><span class="hide-menu">Browse</span></a></li>
                <?php endif; ?>
                
                <?php if (in_array('policy', $module_permissions) && in_array('policy-document', $logged_userpermissions)) : ?>
                    <li class="sidebar-item"> <a class="sidebar-link waves-effect waves-dark" href="index.php?route=policy/documents" aria-expanded="false"><i class="mdi mdi-content-copy"></i><span class="hide-menu">Documents</span></a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <!-- End Sidebar navigation -->
    </div>
    <!-- End Sidebar scroll-->
</aside>