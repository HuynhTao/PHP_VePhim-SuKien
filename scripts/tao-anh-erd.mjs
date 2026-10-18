import fs from 'node:fs/promises';
import { createRequire } from 'node:module';
import { fileURLToPath } from 'node:url';
const require = createRequire(import.meta.url);
const sharp = require('C:/Users/khang/.cache/codex-runtimes/codex-primary-runtime/dependencies/node/node_modules/sharp');

const outDir = new URL('../docs/', import.meta.url);
const W = 2380;
const H = 1510;
const cardW = 420;
const xs = [60, 520, 980, 1440, 1900];
const ys = [180, 630, 1080];

const tables = [
  { name: 'DIA_DIEM', x: xs[0], y: ys[0], fields: [['id', 'BIGINT', 'PK'], ['ten_dia_diem', 'VARCHAR', ''], ['dia_chi', 'VARCHAR', ''], ['tinh_thanh', 'VARCHAR', ''], ['so_dien_thoai', 'VARCHAR', ''], ['trang_thai', 'VARCHAR', '']] },
  { name: 'PHONG_CHIEU', x: xs[1], y: ys[0], fields: [['id', 'BIGINT', 'PK'], ['dia_diem_id', 'BIGINT', 'FK'], ['ten_phong', 'VARCHAR', ''], ['loai_phong', 'VARCHAR', ''], ['trang_thai', 'VARCHAR', '']] },
  { name: 'GHE_NGOI', x: xs[2], y: ys[0], fields: [['id', 'BIGINT', 'PK'], ['phong_chieu_id', 'BIGINT', 'FK'], ['hang_ghe', 'VARCHAR', ''], ['so_ghe', 'INT', ''], ['loai_ghe', 'VARCHAR', ''], ['dang_su_dung', 'BOOLEAN', '']] },
  { name: 'SUAT', x: xs[1], y: ys[1], fields: [['id', 'BIGINT', 'PK'], ['noi_dung_id', 'BIGINT', 'FK'], ['phong_chieu_id', 'BIGINT', 'FK'], ['bat_dau_luc', 'DATETIME', ''], ['ket_thuc_luc', 'DATETIME', ''], ['trang_thai', 'VARCHAR', '']] },
  { name: 'GHE_THEO_SUAT', x: xs[2], y: ys[1], fields: [['id', 'BIGINT', 'PK'], ['suat_id', 'BIGINT', 'FK'], ['ghe_ngoi_id', 'BIGINT', 'FK'], ['gia_ve', 'DECIMAL', ''], ['trang_thai', 'VARCHAR', ''], ['giu_den_luc', 'DATETIME', '']] },
  { name: 'NGUOI_DUNG', x: xs[4], y: ys[1], fields: [['id', 'BIGINT', 'PK'], ['ho_ten', 'VARCHAR', ''], ['email', 'VARCHAR', 'UK'], ['mat_khau', 'VARCHAR', ''], ['so_dien_thoai', 'VARCHAR', ''], ['vai_tro', 'VARCHAR', '']] },
  { name: 'NOI_DUNG', x: xs[0], y: ys[1], fields: [['id', 'BIGINT', 'PK'], ['loai_noi_dung', 'VARCHAR', ''], ['tieu_de', 'VARCHAR', ''], ['mo_ta', 'TEXT', ''], ['anh_bia', 'VARCHAR', ''], ['trang_thai', 'VARCHAR', '']] },
  { name: 'PHIM', x: xs[0], y: ys[2], fields: [['noi_dung_id', 'BIGINT', 'PK, FK'], ['thoi_luong_phut', 'INT', ''], ['gioi_han_tuoi', 'VARCHAR', ''], ['ngon_ngu', 'VARCHAR', ''], ['ngay_khoi_chieu', 'DATE', '']] },
  { name: 'SU_KIEN', x: xs[1], y: ys[2], fields: [['noi_dung_id', 'BIGINT', 'PK, FK'], ['don_vi_to_chuc', 'VARCHAR', ''], ['the_loai', 'VARCHAR', ''], ['gioi_han_tuoi', 'VARCHAR', '']] },
  { name: 'DON_HANG', x: xs[3], y: ys[1], fields: [['id', 'BIGINT', 'PK'], ['nguoi_dung_id', 'BIGINT', 'FK'], ['ma_don_hang', 'VARCHAR', 'UK'], ['trang_thai', 'VARCHAR', ''], ['tam_tinh', 'DECIMAL', ''], ['tien_giam', 'DECIMAL', ''], ['tong_tien', 'DECIMAL', ''], ['het_han_luc', 'DATETIME', '']] },
  { name: 'THANH_TOAN', x: xs[3], y: ys[2], fields: [['id', 'BIGINT', 'PK'], ['don_hang_id', 'BIGINT', 'FK'], ['nha_cung_cap', 'VARCHAR', ''], ['ma_giao_dich', 'VARCHAR', ''], ['so_tien', 'DECIMAL', ''], ['trang_thai', 'VARCHAR', ''], ['thanh_toan_luc', 'DATETIME', '']] },
  { name: 'VE', x: xs[2], y: ys[2], fields: [['id', 'BIGINT', 'PK'], ['don_hang_id', 'BIGINT', 'FK'], ['ghe_theo_suat_id', 'BIGINT', 'FK, UK'], ['ma_ve', 'VARCHAR', 'UK'], ['trang_thai', 'VARCHAR', ''], ['gia_ban', 'DECIMAL', ''], ['phat_hanh_luc', 'DATETIME', '']] },
];

const positions = {
  DIA_DIEM: [xs[0], ys[0]], PHONG_CHIEU: [xs[1], ys[0]], GHE_NGOI: [xs[2], ys[0]],
  NOI_DUNG: [xs[0], ys[1]], SUAT: [xs[1], ys[1]], GHE_THEO_SUAT: [xs[2], ys[1]],
  NGUOI_DUNG: [xs[4], ys[1]], DON_HANG: [xs[3], ys[1]],
  PHIM: [xs[0], ys[2]], SU_KIEN: [xs[1], ys[2]], VE: [xs[2], ys[2]], THANH_TOAN: [xs[3], ys[2]],
};
for (const table of tables) [table.x, table.y] = positions[table.name];

const byName = Object.fromEntries(tables.map(t => [t.name, t]));
const edges = [
  ['DIA_DIEM', 'PHONG_CHIEU', 'right', 'left'],
  ['PHONG_CHIEU', 'GHE_NGOI', 'right', 'left'],
  ['PHONG_CHIEU', 'SUAT', 'bottom', 'top'],
  ['GHE_NGOI', 'GHE_THEO_SUAT', 'bottom', 'top'],
  ['SUAT', 'GHE_THEO_SUAT', 'right', 'left'],
  ['NGUOI_DUNG', 'DON_HANG', 'left', 'right'],
  ['NOI_DUNG', 'PHIM', 'bottom', 'top'],
  ['NOI_DUNG', 'SU_KIEN', 'bottom', 'top'],
  ['NOI_DUNG', 'SUAT', 'right', 'left'],
  ['DON_HANG', 'THANH_TOAN', 'bottom', 'top'],
  ['DON_HANG', 'VE', 'bottom', 'top'],
  ['GHE_THEO_SUAT', 'VE', 'bottom', 'top'],
];

function edgePoint(t, side) {
  const h = 45 + t.fields.length * 34;
  if (side === 'left') return [t.x, t.y + h * 0.52];
  if (side === 'right') return [t.x + cardW, t.y + h * 0.52];
  if (side === 'top') return [t.x + cardW * 0.52, t.y];
  return [t.x + cardW * 0.52, t.y + h];
}

function route(a, b, sideA, sideB) {
  const [x1, y1] = edgePoint(byName[a], sideA);
  const [x2, y2] = edgePoint(byName[b], sideB);
  if (sideA === 'right' && sideB === 'left' && x2 > x1) return `M${x1},${y1} L${x2},${y2}`;
  if (sideA === 'bottom' && sideB === 'top' && y2 > y1) return `M${x1},${y1} L${x1},${(y1+y2)/2} L${x2},${(y1+y2)/2} L${x2},${y2}`;
  if (sideA === 'top' && sideB === 'bottom' && y1 > y2) return `M${x1},${y1} L${x1},${(y1+y2)/2} L${x2},${(y1+y2)/2} L${x2},${y2}`;
  if (sideA === 'bottom' && sideB === 'right') return `M${x1},${y1} L${x1},${y2+24} L${x2+28},${y2+24} L${x2+28},${y2} L${x2},${y2}`;
  return `M${x1},${y1} L${(x1+x2)/2},${y1} L${(x1+x2)/2},${y2} L${x2},${y2}`;
}

function drawTable(t) {
  const rowH = 34, headH = 45, h = headH + t.fields.length * rowH;
  const rows = t.fields.map(([name,type,key], i) => {
    const y = t.y + headH + i * rowH;
    const keyColor = key.includes('FK') ? '#1b806b' : '#c45a42';
    return `<rect x="${t.x}" y="${y}" width="${cardW}" height="${rowH}" fill="${i%2 ? '#f4f6f8' : '#ffffff'}"/><line x1="${t.x}" y1="${y+rowH}" x2="${t.x+cardW}" y2="${y+rowH}" stroke="#d8dee5"/><text x="${t.x+16}" y="${y+22}" class="field">${name}</text><text x="${t.x+278}" y="${y+22}" text-anchor="end" class="key" fill="${key ? keyColor : '#9aa4af'}">${key}</text><text x="${t.x+cardW-14}" y="${y+22}" text-anchor="end" class="type">${type}</text>`;
  }).join('');
  return `<g><rect x="${t.x}" y="${t.y}" width="${cardW}" height="${h}" rx="6" fill="#fff" stroke="#c9d2dc" stroke-width="1.5"/><path d="M${t.x+6},${t.y} H${t.x+cardW-6} Q${t.x+cardW},${t.y} ${t.x+cardW},${t.y+6} V${t.y+headH} H${t.x} V${t.y+6} Q${t.x},${t.y} ${t.x+6},${t.y}" fill="#20577f"/><text x="${t.x+17}" y="${t.y+29}" class="tableTitle">${t.name}</text>${rows}</g>`;
}

const lines = edges.map(([a,b,sa,sb]) => `<path d="${route(a,b,sa,sb)}" class="rel" marker-end="url(#arrow)"/>`).join('');
const cards = tables.map(drawTable).join('');
const svg = `<svg xmlns="http://www.w3.org/2000/svg" width="${W}" height="${H}" viewBox="0 0 ${W} ${H}">
<defs><marker id="arrow" markerWidth="10" markerHeight="8" refX="8" refY="4" orient="auto"><path d="M0,0 L9,4 L0,8 Z" fill="#77838e"/></marker><style>
  text{font-family:Arial,Helvetica,sans-serif}.title{font-size:32px;font-weight:700;fill:#233747}.subtitle{font-size:15px;fill:#697987}.tableTitle{font-size:17px;font-weight:700;fill:#fff;letter-spacing:.4px}.field{font-size:15px;fill:#263540}.key{font-size:11px;font-weight:700;letter-spacing:.3px}.type{font-size:12px;fill:#7d8994}.rel{fill:none;stroke:#788591;stroke-width:1.8;stroke-dasharray:6 5;opacity:.82}
</style></defs>
<rect width="100%" height="100%" fill="#ffffff"/>
<text x="${W/2}" y="59" text-anchor="middle" class="title">ERD - Website dat ve phim va su kien</text>
<text x="${W/2}" y="88" text-anchor="middle" class="subtitle">Mo hinh dat ve phim va su kien | Ten bang va cot tieng Viet khong dau</text>
<g transform="translate(1810,103)"><text x="0" y="0" class="key" fill="#c45a42">PK  Khoa chinh</text><text x="150" y="0" class="key" fill="#1b806b">FK  Khoa ngoai</text><text x="300" y="0" class="key" fill="#9aa4af">UK  Duy nhat</text></g>
${lines}${cards}
<text x="60" y="1470" class="subtitle">Gia tien dung DECIMAL | Giu ghe theo tung suat de tranh ban trung</text>
</svg>`;

await fs.writeFile(new URL('ERD-dat-ve-phim-su-kien.svg', outDir), svg, 'utf8');
await sharp(Buffer.from(svg)).png({ compressionLevel: 9 }).toFile(fileURLToPath(new URL('ERD-dat-ve-phim-su-kien.png', outDir)));
console.log('Created docs/ERD-dat-ve-phim-su-kien.svg and .png');
