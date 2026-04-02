<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Maternity & Delivery Services
        Service::firstOrCreate([
            'code' => 'PREG_TEST'
        ], [
            'name' => 'Pregnancy test',
            'code' => 'PREG_TEST',
            'description' => 'A test to determine pregnancy status using urine or blood sample analysis.',
            'type' => 'single',
            'service_type' => 'laboratory',
            'category' => 'obstetrics_gynecology',
            'base_price' => 20.00,
            'philhealth_covered' => true,
            'philhealth_price' => 15.00,
            'duration_minutes' => 15,
            'status' => 'active',
            'requires_appointment' => false,
            'preparation_instructions' => 'No special preparation required. Test can be done at any time of day.',
        ]);

        Service::firstOrCreate([
            'code' => 'POST_CHECK'
        ], [
            'name' => 'Postpartum Checkup',
            'code' => 'POST_CHECK',
            'description' => 'A comprehensive postpartum checkup to ensure the health and well-being of both mother and baby after childbirth.',
            'type' => 'single',
            'service_type' => 'consultation',
            'category' => 'obstetrics_gynecology',
            'base_price' => 100.00,
            'philhealth_covered' => true,
            'philhealth_price' => 80.00,
            'duration_minutes' => 45,
            'status' => 'active',
            'requires_appointment' => true,
            'preparation_instructions' => 'Bring baby\'s vaccination record and any postpartum concerns.',
            'post_service_instructions' => 'Follow up in 6 weeks for complete postpartum evaluation.',
        ]);

        Service::firstOrCreate([
            'code' => 'NORMAL_DEL'
        ], [
            'name' => 'Normal Spontaneous Package',
            'code' => 'NORMAL_DEL',
            'description' => 'A comprehensive package for normal spontaneous deliveries, including prenatal care, delivery, and postpartum care.',
            'type' => 'package',
            'service_type' => 'delivery',
            'category' => 'obstetrics_gynecology',
            'base_price' => 1500.00,
            'philhealth_covered' => true,
            'philhealth_price' => 1200.00,
            'duration_minutes' => 120,
            'status' => 'active',
            'requires_appointment' => true,
            'available_emergency' => true,
            'staff_requirements' => 'Obstetrician, midwife, and nursing staff',
            'required_equipment' => 'Delivery bed, fetal monitor, neonatal resuscitation equipment',
        ]);

        Service::firstOrCreate([
            'code' => 'PRENATAL'
        ], [
            'name' => 'Prenatal Checkup',
            'code' => 'PRENATAL',
            'description' => 'A comprehensive prenatal checkup to monitor the health and development of the baby and the well-being of the mother during pregnancy.',

            'type' => 'single',
            'service_type' => 'consultation',
            'category' => 'obstetrics_gynecology',
            'base_price' => 75.00,
            'philhealth_covered' => true,
            'philhealth_price' => 60.00,
            'duration_minutes' => 30,
            'status' => 'active',
            'requires_appointment' => true,
            'preparation_instructions' => 'Bring previous prenatal records and any concerns.',
            'quality_indicators' => 'Blood pressure, weight, fetal heart rate monitoring',
        ]);

        Service::firstOrCreate([
            'code' => 'ULTRASOUND'
        ], [
            'name' => 'Ultrasound Scan',
            'code' => 'ULTRASOUND',
            'description' => 'A non-invasive imaging test that uses sound waves to create images of the inside of the body, commonly used during pregnancy to monitor fetal development.',
            'type' => 'single',
            'service_type' => 'imaging',
            'category' => 'obstetrics_gynecology',
            'base_price' => 150.00,
            'philhealth_covered' => true,
            'philhealth_price' => 120.00,
            'duration_minutes' => 30,
            'status' => 'active',
            'requires_appointment' => true,
            'required_equipment' => 'Ultrasound machine with Doppler capability',
            'preparation_instructions' => 'Drink plenty of water before the scan. Wear comfortable clothing.',
        ]);

        Service::firstOrCreate([
            'code' => 'NEWBORN_SCR'
        ], [
            'name' => 'Newborn Screening Test',
            'code' => 'NEWBORN_SCR',
            'description' => 'A series of tests performed on newborns to screen for certain genetic, metabolic, hormonal, and functional conditions that may not be apparent at birth but can cause serious health problems if not detected and treated early.',
            'type' => 'single',
            'service_type' => 'laboratory',
            'category' => 'pediatrics',
            'base_price' => 50.00,
            'philhealth_covered' => true,
            'philhealth_price' => 40.00,
            'duration_minutes' => 20,
            'status' => 'active',
            'requires_appointment' => false,
            'preparation_instructions' => 'Test performed within 24-48 hours after birth.',
            'regulatory_requirements' => 'Required by law for all newborns in the Philippines',
        ]);

        Service::firstOrCreate([
            'code' => 'HEARING_TEST'
        ], [
            'name' => 'Newborn Hearing Test',
            'code' => 'HEARING_TEST',
            'description' => 'A screening test performed on newborns to assess their hearing ability and identify any potential hearing loss or issues that may require further evaluation and intervention.',
            'type' => 'single',
            'service_type' => 'laboratory',
            'category' => 'pediatrics',
            'base_price' => 40.00,
            'philhealth_covered' => true,
            'philhealth_price' => 32.00,
            'duration_minutes' => 15,
            'status' => 'active',
            'requires_appointment' => false,
            'required_equipment' => 'Otoacoustic emissions (OAE) testing equipment',
            'preparation_instructions' => 'Test performed while baby is sleeping.',
        ]);

        Service::firstOrCreate([
            'code' => 'NEWBORN_PKG'
        ], [
            'name' => 'Newborn Package',
            'code' => 'NEWBORN_PKG',
            'description' => 'A comprehensive package for newborn care, including initial assessments, screenings, and essential health services for the baby\'s well-being.',
            'type' => 'package',
            'service_type' => 'consultation',
            'category' => 'pediatrics',
            'base_price' => 300.00,
            'philhealth_covered' => true,
            'philhealth_price' => 240.00,
            'duration_minutes' => 60,
            'status' => 'active',
            'requires_appointment' => true,
            'preparation_instructions' => 'Bring baby\'s birth certificate and parent IDs.',
            'quality_indicators' => 'Complete newborn assessment including APGAR score, vital signs, and feeding evaluation',
        ]);

        Service::firstOrCreate([
            'code' => 'IMMUNIZATION'
        ], [
            'name' => 'Immunization',
            'code' => 'IMMUNIZATION',
            'description' => 'A service that provides vaccinations to protect against various infectious diseases, promoting overall health and immunity.',
            'type' => 'single',
            'service_type' => 'vaccination',
            'category' => 'preventive_care',
            'base_price' => 25.00,
            'philhealth_covered' => true,
            'philhealth_price' => 20.00,
            'duration_minutes' => 15,
            'status' => 'active',
            'requires_appointment' => true,
            'required_supplies' => 'Vaccines stored according to cold chain requirements',
            'documentation_requirements' => 'Vaccination record must be updated in patient\'s health record',
        ]);

        Service::firstOrCreate([
            'code' => 'EAR_PIERCE'
        ], [
            'name' => 'Ear Piercing',
            'code' => 'EAR_PIERCE',
            'description' => 'A service that involves creating a small hole in the earlobe to accommodate earrings, typically performed in a safe and hygienic manner.',
            'type' => 'single',
            'service_type' => 'procedure',
            'category' => 'general_practice',
            'base_price' => 30.00,
            'philhealth_covered' => false,
            'duration_minutes' => 10,
            'status' => 'active',
            'requires_appointment' => false,
            'required_equipment' => 'Sterilized piercing equipment',
            'post_service_instructions' => 'Clean piercing twice daily with saline solution. Avoid swimming for 2 weeks.',
        ]);

        Service::firstOrCreate([
            'code' => 'FP_CONSULT'
        ], [
            'name' => 'Family Planning Consultation',
            'code' => 'FP_CONSULT',
            'description' => 'A consultation service that provides information and guidance on various family planning methods and options to help individuals and couples make informed decisions about their reproductive health.',
            'type' => 'single',
            'service_type' => 'consultation',
            'category' => 'preventive_care',
            'base_price' => 50.00,
            'philhealth_covered' => true,
            'philhealth_price' => 40.00,
            'duration_minutes' => 30,
            'status' => 'active',
            'requires_appointment' => true,
            'consent_form_required' => 'Family planning consent form must be signed',
            'documentation_requirements' => 'Method chosen and counseling provided must be documented',
        ]);

        Service::firstOrCreate([
            'code' => 'DMPSA_INJ'
        ], [
            'name' => 'DMPSA (Injectable Contraceptive)',
            'code' => 'DMPSA_INJ',
            'description' => 'A long-acting injectable contraceptive that provides effective birth control for up to three months, helping to prevent unintended pregnancies.',
            'type' => 'single',
            'service_type' => 'procedure',
            'category' => 'preventive_care',
            'base_price' => 75.00,
            'philhealth_covered' => true,
            'philhealth_price' => 60.00,
            'duration_minutes' => 15,
            'status' => 'active',
            'requires_appointment' => true,
            'required_supplies' => 'DMPSA injection, syringes, alcohol swabs',
            'post_service_instructions' => 'Return in 3 months for next injection. Watch for side effects.',
            'contraindications' => 'Not recommended for women with history of blood clots or breast cancer',
        ]);

        Service::firstOrCreate([
            'code' => 'IMPLANT'
        ], [
            'name' => 'Subdermal Implant',
            'code' => 'IMPLANT',
            'description' => 'A small, flexible rod that is inserted under the skin of the upper arm to provide long-term contraception for up to three years, offering a convenient and effective birth control option.',
            'type' => 'single',
            'service_type' => 'procedure',
            'category' => 'preventive_care',
            'base_price' => 200.00,
            'philhealth_covered' => true,
            'philhealth_price' => 160.00,
            'duration_minutes' => 30,
            'status' => 'active',
            'requires_appointment' => true,
            'required_equipment' => 'Sterile implant insertion kit',
            'required_supplies' => 'Implanon or similar subdermal contraceptive implant',
            'staff_requirements' => 'Trained healthcare provider certified in implant insertion',
            'post_service_instructions' => 'Keep area clean and dry. Return for removal after 3 years or if complications arise.',
            'contraindications' => 'Not recommended for women with active liver disease or unexplained vaginal bleeding',
        ]);
    }
}
