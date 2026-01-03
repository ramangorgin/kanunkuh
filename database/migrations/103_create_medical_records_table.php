<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // Inusarance
            $table->date('insurance_issue_date')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->string('insurance_file')->nullable();

            // Pyhisical details
            $table->string('blood_type', 5)->nullable();
            $table->smallInteger('height')->nullable();
            $table->smallInteger('weight')->nullable();

            // Medical questions
            $table->boolean('head_injury')->nullable();
            $table->text('head_injury_details')->nullable();
            $table->boolean('eye_ear_problems')->nullable();
            $table->text('eye_ear_problems_details')->nullable();
            $table->boolean('seizures')->nullable();
            $table->text('seizures_details')->nullable();
            $table->boolean('respiratory')->nullable();
            $table->text('respiratory_details')->nullable();
            $table->boolean('heart')->nullable();
            $table->text('heart_details')->nullable();
            $table->boolean('blood_pressure')->nullable();
            $table->text('blood_pressure_details')->nullable();
            $table->boolean('blood_disorders')->nullable();
            $table->text('blood_disorders_details')->nullable();
            $table->boolean('diabetes_hepatitis')->nullable();
            $table->text('diabetes_hepatitis_details')->nullable();
            $table->boolean('stomach')->nullable();
            $table->text('stomach_details')->nullable();
            $table->boolean('kidney')->nullable();
            $table->text('kidney_details')->nullable();
            $table->boolean('mental')->nullable();
            $table->text('mental_details')->nullable();
            $table->boolean('addiction')->nullable();
            $table->text('addiction_details')->nullable();
            $table->boolean('surgery')->nullable();
            $table->text('surgery_details')->nullable();
            $table->boolean('skin_allergy')->nullable();
            $table->text('skin_allergy_details')->nullable();
            $table->boolean('drug_allergy')->nullable();
            $table->text('drug_allergy_details')->nullable();
            $table->boolean('insect_allergy')->nullable();
            $table->text('insect_allergy_details')->nullable();
            $table->boolean('dust_allergy')->nullable();
            $table->text('dust_allergy_details')->nullable();
            $table->boolean('medications')->nullable();
            $table->text('medications_details')->nullable();
            $table->boolean('bone_joint')->nullable();
            $table->text('bone_joint_details')->nullable();
            $table->boolean('hiv')->nullable();
            $table->text('hiv_details')->nullable();
            $table->boolean('treatment')->nullable();
            $table->text('treatment_details')->nullable();

            // Extra Explonations
            $table->text('other_conditions')->nullable();

            // Terms
            $table->boolean('commitment_signed')->default(0);


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
