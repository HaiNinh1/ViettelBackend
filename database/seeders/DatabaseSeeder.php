<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Employee;
use App\Models\Contract;
use App\Models\Project;
use App\Models\CertificateType;
use App\Models\Certificate;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create users first
        $this->call(UserSeeder::class);
        
        // 1. Tạo Phòng ban
        $departments = [
            ['name' => 'Phòng Kỹ thuật', 'code' => 'IT', 'description' => 'Phòng công nghệ thông tin'],
            ['name' => 'Phòng Nhân sự', 'code' => 'HR', 'description' => 'Phòng quản lý nhân sự'],
            ['name' => 'Phòng Kinh doanh', 'code' => 'SALES', 'description' => 'Phòng kinh doanh'],
            ['name' => 'Phòng Kế toán', 'code' => 'ACC', 'description' => 'Phòng kế toán tài chính'],
            ['name' => 'Phòng Marketing', 'code' => 'MKT', 'description' => 'Phòng marketing'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }

        // 2. Tạo Loại chứng chỉ
        $certificateTypes = [
            ['name' => 'PMP', 'code' => 'PMP', 'description' => 'Project Management Professional', 'validity_period' => 36, 'required_renewal' => true],
            ['name' => 'TOEIC', 'code' => 'TOEIC', 'description' => 'Test of English for International Communication', 'validity_period' => 24, 'required_renewal' => true],
            ['name' => 'Lái xe B2', 'code' => 'B2', 'description' => 'Giấy phép lái xe hạng B2', 'validity_period' => 120, 'required_renewal' => true],
            ['name' => 'An toàn lao động', 'code' => 'ATLĐ', 'description' => 'Chứng chỉ an toàn lao động', 'validity_period' => 12, 'required_renewal' => true],
            ['name' => 'AWS Certified', 'code' => 'AWS', 'description' => 'Amazon Web Services Certification', 'validity_period' => 36, 'required_renewal' => true],
            ['name' => 'IELTS', 'code' => 'IELTS', 'description' => 'International English Language Testing System', 'validity_period' => 24, 'required_renewal' => false],
            ['name' => 'Scrum Master', 'code' => 'CSM', 'description' => 'Certified Scrum Master', 'validity_period' => 24, 'required_renewal' => true],
            ['name' => 'CCNA', 'code' => 'CCNA', 'description' => 'Cisco Certified Network Associate', 'validity_period' => 36, 'required_renewal' => true],
            ['name' => 'ISO 9001', 'code' => 'ISO9001', 'description' => 'Chứng chỉ ISO 9001', 'validity_period' => 12, 'required_renewal' => true],
            ['name' => 'First Aid', 'code' => 'FA', 'description' => 'Chứng chỉ sơ cứu', 'validity_period' => 24, 'required_renewal' => true],
        ];

        foreach ($certificateTypes as $type) {
            CertificateType::create($type);
        }

        // 3. Tạo Nhân sự
        $employees = [
            ['full_name' => 'Nguyễn Văn A', 'employee_code' => 'NV001', 'email' => 'nva@company.com', 'phone' => '0901234567', 'department_id' => 1, 'position' => 'Senior Developer', 'status' => 'active'],
            ['full_name' => 'Trần Thị B', 'employee_code' => 'NV002', 'email' => 'ttb@company.com', 'phone' => '0901234568', 'department_id' => 1, 'position' => 'Developer', 'status' => 'active'],
            ['full_name' => 'Lê Văn C', 'employee_code' => 'NV003', 'email' => 'lvc@company.com', 'phone' => '0901234569', 'department_id' => 2, 'position' => 'HR Manager', 'status' => 'active'],
            ['full_name' => 'Phạm Thị D', 'employee_code' => 'NV004', 'email' => 'ptd@company.com', 'phone' => '0901234570', 'department_id' => 3, 'position' => 'Sales Manager', 'status' => 'active'],
            ['full_name' => 'Hoàng Văn E', 'employee_code' => 'NV005', 'email' => 'hve@company.com', 'phone' => '0901234571', 'department_id' => 1, 'position' => 'Tech Lead', 'status' => 'active'],
            ['full_name' => 'Vũ Thị F', 'employee_code' => 'NV006', 'email' => 'vtf@company.com', 'phone' => '0901234572', 'department_id' => 4, 'position' => 'Accountant', 'status' => 'active'],
            ['full_name' => 'Đỗ Văn G', 'employee_code' => 'NV007', 'email' => 'dvg@company.com', 'phone' => '0901234573', 'department_id' => 5, 'position' => 'Marketing Manager', 'status' => 'active'],
            ['full_name' => 'Bùi Thị H', 'employee_code' => 'NV008', 'email' => 'bth@company.com', 'phone' => '0901234574', 'department_id' => 1, 'position' => 'Junior Developer', 'status' => 'active'],
            ['full_name' => 'Đinh Văn I', 'employee_code' => 'NV009', 'email' => 'dvi@company.com', 'phone' => '0901234575', 'department_id' => 3, 'position' => 'Sales Executive', 'status' => 'active'],
            ['full_name' => 'Ngô Thị K', 'employee_code' => 'NV010', 'email' => 'ntk@company.com', 'phone' => '0901234576', 'department_id' => 2, 'position' => 'HR Executive', 'status' => 'active'],
        ];

        foreach ($employees as $emp) {
            Employee::create($emp);
        }

        // 4. Tạo Hợp đồng
        for ($i = 1; $i <= 10; $i++) {
            Contract::create([
                'employee_id' => $i,
                'contract_number' => 'HD' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'contract_type' => ['Thử việc', 'Chính thức', 'Hợp đồng'][rand(0, 2)],
                'start_date' => Carbon::now()->subMonths(rand(1, 24)),
                'end_date' => Carbon::now()->addMonths(rand(12, 36)),
                'salary' => rand(10, 50) * 1000000,
                'status' => 'active',
            ]);
        }

        // 5. Tạo Dự án
        $projects = [
            ['name' => 'Dự án Website Ecommerce', 'code' => 'PRJ001', 'description' => 'Xây dựng website bán hàng', 'client' => 'Công ty ABC', 'start_date' => Carbon::now()->subMonths(6), 'end_date' => Carbon::now()->addMonths(6), 'status' => 'Đang thực hiện'],
            ['name' => 'Dự án Mobile App', 'code' => 'PRJ002', 'description' => 'Phát triển ứng dụng di động', 'client' => 'Công ty XYZ', 'start_date' => Carbon::now()->subMonths(3), 'end_date' => Carbon::now()->addMonths(9), 'status' => 'Đang thực hiện'],
            ['name' => 'Dự án ERP System', 'code' => 'PRJ003', 'description' => 'Triển khai hệ thống ERP', 'client' => 'Công ty DEF', 'start_date' => Carbon::now()->subMonths(12), 'end_date' => Carbon::now()->subMonths(1), 'status' => 'Hoàn thành'],
            ['name' => 'Dự án CRM', 'code' => 'PRJ004', 'description' => 'Xây dựng hệ thống CRM', 'client' => 'Công ty GHI', 'start_date' => Carbon::now()->subMonths(2), 'end_date' => null, 'status' => 'Tạm dừng'],
            ['name' => 'Dự án AI Chatbot', 'code' => 'PRJ005', 'description' => 'Phát triển chatbot AI', 'client' => 'Công ty JKL', 'start_date' => Carbon::now()->subMonths(1), 'end_date' => Carbon::now()->addMonths(12), 'status' => 'Đang thực hiện'],
        ];

        foreach ($projects as $proj) {
            $project = Project::create($proj);
            
            // Phân công nhân sự vào dự án
            $employeeIds = [1, 2, 5, 8]; // IT department employees
            foreach ($employeeIds as $empId) {
                $project->employees()->attach($empId, [
                    'role' => ['Developer', 'Tech Lead', 'Tester'][rand(0, 2)],
                    'joined_date' => $project->start_date,
                ]);
            }
        }

        // 6. Tạo Chứng chỉ
        $certificates = [
            // Còn hạn
            ['employee_id' => 1, 'certificate_type_id' => 1, 'certificate_number' => 'PMP001', 'issued_by' => 'PMI', 'issued_date' => Carbon::now()->subMonths(6), 'expiry_date' => Carbon::now()->addMonths(30), 'status' => 'Còn hạn'],
            ['employee_id' => 1, 'certificate_type_id' => 5, 'certificate_number' => 'AWS001', 'issued_by' => 'Amazon', 'issued_date' => Carbon::now()->subMonths(12), 'expiry_date' => Carbon::now()->addMonths(24), 'status' => 'Còn hạn'],
            ['employee_id' => 2, 'certificate_type_id' => 2, 'certificate_number' => 'TOEIC001', 'issued_by' => 'ETS', 'issued_date' => Carbon::now()->subMonths(3), 'expiry_date' => Carbon::now()->addMonths(21), 'status' => 'Còn hạn'],
            ['employee_id' => 5, 'certificate_type_id' => 7, 'certificate_number' => 'CSM001', 'issued_by' => 'Scrum Alliance', 'issued_date' => Carbon::now()->subMonths(8), 'expiry_date' => Carbon::now()->addMonths(16), 'status' => 'Còn hạn'],
            
            // Sắp hết hạn (trong vòng 30 ngày)
            ['employee_id' => 3, 'certificate_type_id' => 4, 'certificate_number' => 'ATLĐ001', 'issued_by' => 'Sở LĐTBXH', 'issued_date' => Carbon::now()->subMonths(11), 'expiry_date' => Carbon::now()->addDays(20), 'status' => 'Sắp hết hạn'],
            ['employee_id' => 4, 'certificate_type_id' => 9, 'certificate_number' => 'ISO001', 'issued_by' => 'ISO', 'issued_date' => Carbon::now()->subMonths(11), 'expiry_date' => Carbon::now()->addDays(25), 'status' => 'Sắp hết hạn'],
            ['employee_id' => 6, 'certificate_type_id' => 4, 'certificate_number' => 'ATLĐ002', 'issued_by' => 'Sở LĐTBXH', 'issued_date' => Carbon::now()->subMonths(11), 'expiry_date' => Carbon::now()->addDays(15), 'status' => 'Sắp hết hạn'],
            
            // Hết hạn
            ['employee_id' => 7, 'certificate_type_id' => 2, 'certificate_number' => 'TOEIC002', 'issued_by' => 'ETS', 'issued_date' => Carbon::now()->subMonths(26), 'expiry_date' => Carbon::now()->subDays(10), 'status' => 'Hết hạn'],
            ['employee_id' => 8, 'certificate_type_id' => 10, 'certificate_number' => 'FA001', 'issued_by' => 'Red Cross', 'issued_date' => Carbon::now()->subMonths(25), 'expiry_date' => Carbon::now()->subDays(5), 'status' => 'Hết hạn'],
            ['employee_id' => 9, 'certificate_type_id' => 3, 'certificate_number' => 'B2001', 'issued_by' => 'Sở GTVT', 'issued_date' => Carbon::now()->subMonths(121), 'expiry_date' => Carbon::now()->subDays(30), 'status' => 'Hết hạn'],
        ];

        foreach ($certificates as $cert) {
            Certificate::create($cert);
        }

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('📊 Created:');
        $this->command->info('   - 5 Departments');
        $this->command->info('   - 10 Certificate Types');
        $this->command->info('   - 10 Employees');
        $this->command->info('   - 10 Contracts');
        $this->command->info('   - 5 Projects');
        $this->command->info('   - 10 Certificates (4 active, 3 expiring soon, 3 expired)');
    }
}
