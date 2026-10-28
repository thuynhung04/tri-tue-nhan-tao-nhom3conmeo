// ============================================================
// HỆ TƯ VẤN LỘ TRÌNH HỌC TẬP - Vue.js Application
// ============================================================

// Tự động phát hiện base URL để fetch API qua HTTP (không phải file://)
const _loc = window.location;
const BASE_URL = (_loc.protocol === 'file:')
  ? 'http://localhost/expert-system'
  : _loc.origin + _loc.pathname.replace(/\/[^/]*$/, '');

const { createApp, ref, reactive, computed } = Vue;


// ── Category meta (icon + label) ────────────────────────────
const CATEGORY_META = {
  ai:       { icon: '🤖', label: 'AI/ML',             color: '#6366f1' },
  data:     { icon: '📊', label: 'Data Science',       color: '#8b5cf6' },
  web:      { icon: '🌐', label: 'Web Development',    color: '#f97316' },
  software: { icon: '💻', label: 'Software Eng.',      color: '#10b981' },
  security: { icon: '🔒', label: 'Cybersecurity',      color: '#ef4444' },
  system:   { icon: '⚙️', label: 'System/DevOps',      color: '#0ea5e9' },
  warning:  { icon: '⚠️', label: 'Cảnh báo',          color: '#f59e0b' },
  danger:   { icon: '🚨', label: 'Nguy cơ học vụ',    color: '#dc2626' },
  success:  { icon: '✅', label: 'Tốt',                color: '#16a34a' },
  info:     { icon: 'ℹ️', label: 'Thông tin',         color: '#0284c7' },
};

// ── Backward chaining goal options ──────────────────────────
const GOAL_OPTIONS = [
  // Định hướng chuyên ngành
  { value: 'goi_y_ai_ml',               label: '🎯 Gợi ý hướng AI/ML'                     },
  { value: 'goi_y_data_science',         label: '🎯 Gợi ý hướng Data Science'              },
  { value: 'goi_y_web_dev',             label: '🎯 Gợi ý hướng Web Development'            },
  { value: 'goi_y_software_engineering',label: '🎯 Gợi ý hướng Software Eng.'              },
  { value: 'goi_y_cybersecurity',        label: '🎯 Gợi ý hướng Cybersecurity'             },
  { value: 'goi_y_devops',              label: '🎯 Gợi ý hướng DevOps'                     },
  // Kiểm tra điều kiện môn học kỳ tới
  { value: 'co_the_hoc_csdl',           label: '📚 Đủ điều kiện học Cơ sở dữ liệu'         },
  { value: 'co_the_hoc_xstk',           label: '📚 Đủ điều kiện học Xác suất Thống kê'     },
  { value: 'co_the_hoc_mmt',            label: '📚 Đủ điều kiện học Mạng máy tính'         },
  { value: 'co_the_hoc_hdt',            label: '📚 Đủ điều kiện học Hệ điều hành'          },
  { value: 'co_the_hoc_ltdt',           label: '📚 Đủ điều kiện học Lý thuyết đồ thị'      },
  { value: 'co_the_hoc_cnpm',           label: '📚 Đủ điều kiện học Công nghệ phần mềm'    },
  { value: 'co_the_hoc_ttnt',           label: '📚 Đủ điều kiện học Trí tuệ nhân tạo'      },
  { value: 'co_the_hoc_bmtt',           label: '📚 Đủ điều kiện học Bảo mật thông tin'     },
  { value: 'co_the_hoc_ptud_desktop',   label: '📚 Đủ điều kiện học Ứng dụng Desktop'      },
  { value: 'co_the_hoc_java',           label: '📚 Đủ điều kiện học Công nghệ Java'        },
  { value: 'co_the_hoc_mobile',         label: '📚 Đủ điều kiện học Lập trình di động'     },
  // Cảnh báo & khác
  { value: 'co_the_dang_ky_nang_cao',   label: '⭐ Đủ điều kiện đăng ký môn nâng cao'      },
  { value: 'nen_giam_tin_chi',          label: '⚠️ Cần giảm số tín chỉ'                    },
  { value: 'canh_bao_hoc_vu',           label: '🚨 Nguy cơ cảnh báo học vụ'                },
  { value: 'co_the_dang_ky_luan_van',   label: '🎓 Đủ điều kiện làm Luận văn tốt nghiệp'   },
];

createApp({
  setup() {

    // ── State ─────────────────────────────────────────────
    const activeTab = ref('input');

    const form = reactive({
      diem_tb:             '',
      ky_nang_lap_trinh:   'trung_binh',
      ky_nang_toan:        'trung_binh',
      ky_nang_mem:         'trung_binh',
      so_thich:            'web',
      so_tin_chi_dang_ky:      18,
      tin_chi_tich_luy:        0,
      biet_html:               false,
      biet_python:             false,
      biet_oop:                false,
      biet_mang:               false,
      // Môn chưa đạt
      chua_dat_nhap_mon:       false,
      chua_dat_toan_cao_cap:   false,
      chua_dat_ctdl:           false,
      chua_dat_ktlt:           false,
      chua_dat_oop_mon:        false,
      // Môn đã hoàn thành
      hoan_thanh_nhap_mon:     false,
      hoan_thanh_toan_cao_cap: false,
      hoan_thanh_ktlt:         false,
      hoan_thanh_oop:          false,
      hoan_thanh_ctdl:         false,
      // Môn muốn học
      muon_hoc_xstk:           false,
      muon_hoc_mmt:            false,
      muon_hoc_hdt:            false,
      muon_hoc_csdl:           false,
      muon_hoc_ltdt:           false,
      muon_hoc_cnpm:           false,
      muon_hoc_ttnt:           false,
      muon_hoc_bmtt:           false,
      muon_hoc_ptud_desktop:   false,
      muon_hoc_java:           false,
      muon_hoc_mobile:         false,
    });

    const method       = ref('forward');
    const goalBC       = ref('goi_y_ai_ml');
    const loading      = ref(false);
    const result       = ref(null);
    const error        = ref('');
    const traceVisible = ref(false);  // Thu gọn mặc định
    const students     = ref([]);
    const allRules  = ref([]);

    // ── Computed ──────────────────────────────────────────
    const catMeta = computed(() => CATEGORY_META);
    const goalOptions = computed(() => GOAL_OPTIONS);

    // ── Methods ───────────────────────────────────────────
    function setTab(tab) { activeTab.value = tab; }

    function getCatMeta(cat) {
      return CATEGORY_META[cat] || { icon: '📌', label: cat, color: '#64748b' };
    }

    async function runInference() {
      error.value = '';
      if (form.diem_tb === '' || isNaN(parseFloat(form.diem_tb)) || form.diem_tb < 0 || form.diem_tb > 10) {
        error.value = 'Vui lòng nhập Điểm trung bình hợp lệ (ví dụ: 7.5)';
        return;
      }

      if (form.so_tin_chi_dang_ky === '' || isNaN(parseInt(form.so_tin_chi_dang_ky)) || form.so_tin_chi_dang_ky < 0 || form.so_tin_chi_dang_ky > 60) {
        error.value = 'Vui lòng nhập Số tín chỉ đăng ký hợp lệ (0-60)';
        return;
      }

      if (form.tin_chi_tich_luy === '' || isNaN(parseInt(form.tin_chi_tich_luy)) || form.tin_chi_tich_luy < 0 || form.tin_chi_tich_luy > 160) {
        error.value = 'Vui lòng nhập Số tín chỉ tích lũy hợp lệ (0-160)';
        return;
      }

      loading.value = true;
      result.value  = null;

      const payload = {
        facts: buildFacts(),
        method: method.value,
        goal:   method.value === 'backward' ? goalBC.value : null,
      };

      try {
        const res = await fetch(`${BASE_URL}/api/inference.php`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(payload),
        });
        const data = await res.json();
        if (!data.success) throw new Error(data.error || 'Lỗi không xác định');
        result.value = data;
        activeTab.value = 'result';
      } catch (e) {
        error.value = 'Lỗi kết nối API: ' + e.message;
      } finally {
        loading.value = false;
      }
    }

    function buildFacts() {
      return {
        diem_tb:                parseFloat(form.diem_tb),
        ky_nang_lap_trinh:      form.ky_nang_lap_trinh,
        ky_nang_toan:           form.ky_nang_toan,
        ky_nang_mem:            form.ky_nang_mem,
        so_thich:               form.so_thich,
        so_tin_chi_dang_ky:     parseInt(form.so_tin_chi_dang_ky),
        tin_chi_tich_luy:       parseInt(form.tin_chi_tich_luy),
        // Kỹ năng
        biet_html:              !!form.biet_html,
        biet_python:            !!form.biet_python,
        biet_oop:               !!form.biet_oop,
        biet_mang:              !!form.biet_mang,
        // Môn chưa đạt
        chua_dat_nhap_mon:      !!form.chua_dat_nhap_mon,
        chua_dat_toan_cao_cap:  !!form.chua_dat_toan_cao_cap,
        chua_dat_ctdl:          !!form.chua_dat_ctdl,
        chua_dat_ktlt:          !!form.chua_dat_ktlt,
        chua_dat_oop_mon:       !!form.chua_dat_oop_mon,
        // Môn đã hoàn thành
        hoan_thanh_nhap_mon:    !!form.hoan_thanh_nhap_mon,
        hoan_thanh_toan_cao_cap:!!form.hoan_thanh_toan_cao_cap,
        hoan_thanh_ktlt:        !!form.hoan_thanh_ktlt,
        hoan_thanh_oop:         !!form.hoan_thanh_oop,
        hoan_thanh_ctdl:        !!form.hoan_thanh_ctdl,
        // Môn muốn học
        muon_hoc_xstk:          !!form.muon_hoc_xstk,
        muon_hoc_mmt:           !!form.muon_hoc_mmt,
        muon_hoc_hdt:           !!form.muon_hoc_hdt,
        muon_hoc_csdl:          !!form.muon_hoc_csdl,
        muon_hoc_ltdt:          !!form.muon_hoc_ltdt,
        muon_hoc_cnpm:          !!form.muon_hoc_cnpm,
        muon_hoc_ttnt:          !!form.muon_hoc_ttnt,
        muon_hoc_bmtt:          !!form.muon_hoc_bmtt,
        muon_hoc_ptud_desktop:  !!form.muon_hoc_ptud_desktop,
        muon_hoc_java:          !!form.muon_hoc_java,
        muon_hoc_mobile:        !!form.muon_hoc_mobile,
      };
    }

    function loadStudent(sv) {
      Object.assign(form, sv.facts);
      // Patch field name differences
      if (sv.facts.so_thich) form.so_thich = sv.facts.so_thich;
      activeTab.value = 'input';
    }

    function resetForm() {
      Object.keys(form).forEach(k => {
        if (typeof form[k] === 'boolean') form[k] = false;
        else if (typeof form[k] === 'number') form[k] = k === 'so_tin_chi_dang_ky' ? 18 : 0;
        else form[k] = '';
      });
      form.ky_nang_lap_trinh = 'trung_binh';
      form.ky_nang_toan      = 'trung_binh';
      form.ky_nang_mem       = 'trung_binh';
      form.so_thich          = 'web';
      result.value = null;
      error.value  = '';
    }

    function formattedTrace(lines) {
      return lines.map(l => {
        if (l.includes('✅') || l.includes('✔')) return `<span class="ok">${escHtml(l)}</span>`;
        if (l.includes('✗') || l.includes('❌')) return `<span class="fail">${escHtml(l)}</span>`;
        if (l.startsWith('──'))                   return `<span class="head">${escHtml(l)}</span>`;
        return escHtml(l);
      }).join('\n');
    }

    function escHtml(s) {
      return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function studentInitial(name) {
      return name ? name.split(' ').pop().charAt(0).toUpperCase() : '?';
    }

    function studentBadgeClass(sv) {
      const dtb = sv.facts.diem_tb;
      if (dtb >= 8.0) return 'badge badge-green';
      if (dtb >= 6.0) return 'badge';
      if (dtb >= 4.0) return 'badge badge-yellow';
      return 'badge badge-red';
    }

    function studentBadgeLabel(sv) {
      const dtb = sv.facts.diem_tb;
      if (dtb >= 8.0) return `⭐ Xuất sắc (${dtb})`;
      if (dtb >= 7.0) return `👍 Khá (${dtb})`;
      if (dtb >= 5.0) return `📚 TB (${dtb})`;
      return `⚠️ Yếu (${dtb})`;
    }

    // ── Load dữ liệu ban đầu ──────────────────────────────
    async function loadStudents() {
      try {
        const res = await fetch(`${BASE_URL}/data/students.json`);
        students.value = await res.json();
      } catch { students.value = []; }
    }

    async function loadRules() {
      try {
        const res = await fetch(`${BASE_URL}/api/rules_list.php`);
        allRules.value = await res.json();
      } catch { allRules.value = []; }
    }

    loadStudents();
    loadRules();

    return {
      activeTab, form, method, goalBC, loading, result, error, traceVisible,
      students, allRules, catMeta, goalOptions,
      setTab, getCatMeta, runInference, loadStudent, resetForm,
      formattedTrace, studentInitial, studentBadgeClass, studentBadgeLabel,
      GOAL_OPTIONS,
    };
  }
}).mount('#app');
