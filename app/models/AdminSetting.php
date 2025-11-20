<?php
// app/models/AdminSetting.php
class AdminSetting extends Model {
    public function get($key) {
        $stmt = $this->db->prepare("SELECT value FROM admin_settings WHERE keyname=?");
        $stmt->execute([$key]);
        $r = $stmt->fetch(PDO::FETCH_ASSOC);
        return $r ? $r['value'] : null;
    }
    public function set($key, $value) {
        $stmt = $this->db->prepare("INSERT INTO admin_settings (keyname, value) VALUES (?, ?) ON DUPLICATE KEY UPDATE value=?");
        return $stmt->execute([$key, $value, $value]);
    }
}
