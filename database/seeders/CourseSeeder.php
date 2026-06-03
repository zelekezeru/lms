<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('courses')->insert(array_map(function ($row) {
            return [
                'name' => $row[0],
                'code' => $row[1],
                'credit_hours' => $row[2],
                'duration' => $row[3],
                'description' => $row[4],
                'is_training' => $row[5],
                'status' => $row[6],
                'is_deleted' => $row[7],
                'is_published' => $row[8],
                'is_approved' => $row[9],
                'is_completed' => $row[10],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }, 
        [
            ['OBBS101 OT Survey (ብሉይ ኪዳን ዳሰሳ ጥናት)', 'CR/068', 3, 4, 'BBS101 OT Survey (ብሉይ ኪዳን ዳሰሳ ጥናት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:29:09', '2025-08-10 18:46:49'],
            ['OBBS102 NT Survey (አዲስ ኪዳን ዳሰሳ ጥናት)', 'CR/069', 3, 4, 'BBS102 NT Survey (አዲስ ኪዳን ዳሰሳ ጥናት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:30:33', '2025-08-10 17:30:33'],
            ['OBTS101 Theology I (ስነ-መለኮት-አንድ)', 'CR/070', 3, 4, 'BTS101 Theology I (ስነ-መለኮት-አንድ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:31:24', '2025-08-10 17:31:24'],
            ['OBBS103 Hermeneutics (ስነ-አፈታት)', 'CR/071', 3, 4, 'BBS103 Hermeneutics (ስነ-አፈታት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:32:03', '2025-08-10 17:32:03'],
            ['OBMS101 Personal Prayer (የግል-ጸሎት)', 'CR/072', 3, 4, 'BMS101 Personal Prayer (የግል-ጸሎት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:32:44', '2025-08-10 17:32:44'],
            ['OBTS102 Theology II (ስነ-መለኮት-ሁለት)', 'CR/073', 3, 4, 'BTS102 Theology II (ስነ-መለኮት-ሁለት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:33:22', '2025-08-10 17:33:22'],
            ['OBCP101 Biblical Praise & Worship - የመጽሐፍ ቅዱስ አምልኮ', 'CR/074', 3, 4, 'BCP101 Biblical Praise & Worship - የመጽሐፍ ቅዱስ አምልኮ', 0, 1, 0, 0, 0, 0, '2025-08-10 17:34:38', '2025-08-10 17:34:38'],
            ['OBBS104 Gospel of Matthew (የማቲዎስ ወንጌል)', 'CR/075', 3, 4, 'BBS104 Gospel of Matthew (የማቲዎስ ወንጌል)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:35:15', '2025-08-10 17:35:15'],
            ['OHS101 Church History (የቤተ-ክርስቲያን ታሪክ)', 'CR/076', 3, 4, 'HS101 Church History (የቤተ-ክርስቲያን ታሪክ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:35:55', '2025-08-10 17:35:55'],
            ['OCSF101 Spiritual Formation (መንፈሳዊ ታንጾ)', 'CR/077', 3, 4, 'CSF101 Spiritual Formation (መንፈሳዊ ታንጾ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:36:32', '2025-08-10 17:36:32'],
            ['OBTS201 Pneumatology (ትምህርተ-መንፈስ ቅዱስ)', 'CR/078', 3, 4, 'BTS201 Pneumatology (ትምህርተ-መንፈስ ቅዱስ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:37:08', '2025-08-10 17:37:08'],
            ['OBBS201 Homiletics (ስነ-ስብከት)', 'CR/079', 3, 4, 'BBS201 Homiletics (ስነ-ስብከት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:38:03', '2025-08-10 17:38:03'],
            ['OBBS202 Book of Acts (የሐዋሪያት ስራ)', 'CR/080', 3, 4, 'BBS202 Book of Acts (የሐዋሪያት ስራ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:38:34', '2025-08-10 17:38:34'],
            ['OBMS201 Cross Cultural Communication (ባህል ተሻጋሪ ግንኑኝነት)', 'CR/081', 3, 4, 'BMS201 Cross Cultural Communication (ባህል ተሻጋሪ ግንኑኝነት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:39:05', '2025-08-10 17:39:05'],
            ['OBMS202 Gospel & Culture (ወንጌልና ባህል)', 'CR/082', 3, 4, 'BMS202 Gospel & Culture (ወንጌልና ባህል)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:39:47', '2025-08-10 17:39:47'],
            ['OBBS203 Romans & Galatians (የሮሜና የገላትያ)', 'CR/083', 3, 4, 'BBS203 Romans & Galatians (የሮሜና የገላትያ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:40:19', '2025-08-10 17:40:19'],
            ['OBBS204 Minor Prophets (አሥራ ሁለቱ ታናናሽ ነቢያት)', 'CR/084', 3, 4, 'BBS204 Minor Prophets (አሥራ ሁለቱ ታናናሽ ነቢያት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:40:51', '2025-08-10 17:40:51'],
            ['OBCP201 Disciple Making (ደቀ-መዛሙርትነት)', 'CR/085', 3, 4, 'BCP201 Disciple Making (ደቀ-መዛሙርትነት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:41:23', '2025-08-10 17:41:23'],
            ['OBCP202 Pastoral Ministry (የመጋቢያን አገልግሎት)', 'CR/086', 3, 4, 'BCP202 Pastoral Ministry (የመጋቢያን አገልግሎት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:42:03', '2025-08-10 17:42:03'],
            ['OCL201 BF for Christian Leadership (ክርስቲያናዊ አመራር)', 'CR/087', 3, 4, 'CL201 BF for Christian Leadership (ክርስቲያናዊ አመራር)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:42:35', '2025-08-10 17:42:35'],
            ['OBMS203 History of Mohammed (እስልምና-አንድ)', 'CR/088', 3, 4, 'BMS203 History of Mohammed (እስልምና-አንድ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:43:15', '2025-08-10 17:43:15'],
            ['OBBS301 Corinthians-section-1 (ቆሮንጦስ- ክፍል 1)', 'CR/089', 3, 4, 'BBS301 Corinthians-section-1 (ቆሮንጦስ- ክፍል 1)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:43:50', '2025-08-10 17:43:50'],
            ['OBTS301 Eschatology (የመጨረሻው ዘመን)', 'CR/090', 3, 4, 'BTS301 Eschatology (የመጨረሻው ዘመን)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:44:26', '2025-08-10 17:44:26'],
            ['OBCL301 Church Administration (የቤተ-ክርስቲያን አስተዳደር)', 'CR/091', 3, 4, 'BCL301 Church Administration (የቤተ-ክርስቲያን አስተዳደር)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:44:58', '2025-08-10 17:44:58'],
            ['OBMS301 Christian Islam Controversy (እስልምና- ሁለት)', 'CR/092', 3, 4, 'BMS301 Christian Islam Controversy (እስልምና- ሁለት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:45:33', '2025-08-10 17:45:33'],
            ['OGS301 Psychology (ስነ-ልቦና)', 'CR/093', 3, 4, 'GS301 Psychology (ስነ-ልቦና)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:46:06', '2025-08-10 17:46:06'],
            ['OBBS302 Corinthians-section-2 (ቆሮንጦስ- ክፍል 2)', 'CR/094', 3, 4, 'BBS302 Corinthians-section-2 (ቆሮንጦስ- ክፍል 2)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:46:41', '2025-08-10 17:46:41'],
            ['OCSF301 Understanding Spiritual Gifts (መንፈሳዊ ስጦታዎችን መረዳት)', 'CR/095', 3, 4, 'CSF301 Understanding Spiritual Gifts (መንፈሳዊ ስጦታዎችን መረዳት)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:47:19', '2025-08-10 17:47:19'],
            ['OBBS303 Pentateuch (ፔንታቱክ)', 'CR/096', 3, 4, 'BBS303 Pentateuch (ፔንታቱክ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:47:57', '2025-08-10 17:47:57'],
            ['OBTS302 Christian Ethics (ክርስቲያናዊ ስነ-ምግባር)', 'CR/097', 3, 4, 'BTS302 Christian Ethics (ክርስቲያናዊ ስነ-ምግባር)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:48:30', '2025-08-10 17:48:30'],
            ['OBMS302 Biblical Theology of Mission (ክርስቲያናዊ ተልዕኮ)', 'CR/098', 3, 4, 'BMS302 Biblical Theology of Mission (ክርስቲያናዊ ተልዕኮ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:49:31', '2025-08-10 17:49:31'],
            ['OBBS303 Book of Isaiah (ትንቢተ ኢሳያስ)', 'CR/099', 3, 4, 'BBS303 Book of Isaiah (ትንቢተ ኢሳያስ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:50:04', '2025-08-10 17:50:04'],
            ['OBBS401 Gospel of John (ዮሐንስ ወንጌል)', 'CR/100', 3, 4, 'BBS401 Gospel of John (ዮሐንስ ወንጌል)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:50:55', '2025-08-12 01:58:05'],
            ['OBCP401 Christian Counseling (ክርስቲያናዊ ምክር)', 'CR/101', 3, 4, 'BCP401 Christian Counseling (ክርስቲያናዊ ምክር)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:51:31', '2025-08-12 06:19:44'],
            ['OBCL402 Philosophy of Leadership (የአመራር ፍልስፍና)', 'CR/102', 3, 4, 'BCL402 Philosophy of Leadership (የአመራር ፍልስፍና)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:52:03', '2025-08-12 01:57:09'],
            ['OBCP403 Sociology (ስነ-ማህበረሰብ)', 'CR/103', 3, 4, 'BCP403 Sociology (ስነ-ማህበረሰብ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:52:36', '2025-08-12 01:57:33'],
            ['OBTS401 Ecclesiology & Church Sacrament (ቤ/ክና ቁርባን)', 'CR/104', 3, 4, 'BTS401 Ecclesiology & Church Sacrament (ቤ/ክና ቁርባን)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:53:08', '2025-08-12 01:57:22'],
            ['OBBS402 Pastoral Epistles (የመጋቢያን መልዕክቶች)', 'CR/105', 3, 4, 'BBS402 Pastoral Epistles (የመጋቢያን መልዕክቶች)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:53:42', '2025-08-12 01:56:56'],
            ['OBCP404 Church & Society (ቤተ-ክርስቲያንና ህብረተሰብ)', 'CR/106', 3, 4, 'BCP404 Church & Society (ቤተ-ክርስቲያንና ህብረተሰብ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:54:13', '2025-08-12 01:56:32'],
            ['OBCL401 Conflict Resolution (የግጭት አፈታት ዜዴ)', 'CR/107', 3, 4, 'BCL401 Conflict Resolution (የግጭት አፈታት ዜዴ)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:54:47', '2025-08-12 01:56:08'],
            ['OBTS402 Cults & False Teachings (መናፍቃዊና ሐሰተኛ አስተምህሮቶች)', 'CR/108', 3, 4, 'BTS402 Cults & False Teachings (መናፍቃዊና ሐሰተኛ አስተምህሮቶች)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:55:20', '2025-08-12 01:55:40'],
            ['OBMS401 ATR (African Traditional Religions) (አፍሪካ ባህላዊ ሐይማኖቶች)', 'CR/109', 3, 4, 'BMS401 ATR (African Traditional Religions) (አፍሪካ ባህላዊ ሐይማኖቶች)', 0, 1, 0, 0, 0, 0, '2025-08-10 17:55:55', '2025-08-12 01:55:28'],
        ]));
    }
}
