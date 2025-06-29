<script setup>
import { defineProps, ref } from "vue";
import { router } from "@inertiajs/vue3";
import Swal from "sweetalert2";
import "sweetalert2/dist/sweetalert2.min.css";
import { TrashIcon } from "@heroicons/vue/24/outline";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
  payment: Object,
  student: Object,
});

const receiptRef = ref(null);

const deletePayment = (id) => {
  Swal.fire({
    title: "Are you sure?",
    text: "This will delete the individual payment!",
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#d33",
    cancelButtonColor: "#3085d6",
    confirmButtonText: "Yes, delete it!",
  }).then((result) => {
    if (result.isConfirmed) {
      router.delete(route("payments.destroy", { payment: id }), {
        onSuccess: () => {
          Swal.fire("Deleted!", "The payment has been deleted.", "success");
        },
      });
    }
  });
};

const printReceipt = () => {
  // Focus print on the receipt only
  const printContents = receiptRef.value?.outerHTML;
  if (!printContents) {
    window.print();
    return;
  }
  const originalContents = document.body.innerHTML;
  document.body.innerHTML = printContents;
  window.print();
  document.body.innerHTML = originalContents;
  window.location.reload(); // reload to restore Vue state
};
</script>
<template>
    <AppLayout>
        <div class="mb-4">
            <button
                @click="$inertia.visit(route('students.show', { student: props.student.id }))"
                class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded shadow transition"
            >
                ← Back to Student Profile
            </button>
        </div>

        <div ref="receiptRef" class="receipt-paper mx-auto my-10 print:my-0 bg-white dark:bg-gray-900 shadow print:shadow-none print:border print:border-gray-300 print:p-0 border border-dashed border-gray-400 dark:border-gray-600 rounded-lg relative overflow-hidden transition-colors">
            <!-- Receipt Header -->
            <div class="receipt-header text-center py-6 border-b border-dashed border-gray-400 dark:border-gray-600 bg-white dark:bg-gray-900 transition-colors">
                <div class="font-mono text-xs text-gray-400 dark:text-gray-500 tracking-widest mb-1">*** PAYMENT RECEIPT ***</div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-gray-100">Shiloh International Theological Seminary</h1>
                <div class="font-mono text-xs text-gray-400 dark:text-gray-500 tracking-widest mt-1">------------------------------</div>
            </div>
            
            <!-- Student Info -->
            <div class="px-6 py-4 bg-white dark:bg-gray-900 transition-colors">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-2 mb-3">
                    <div class="flex flex-col gap-1">
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400">
                            <span>Student Name:</span>
                            <span class="font-bold text-gray-800 dark:text-gray-200 text-sm">
                                {{ props.student.firstName }} {{ props.student.middleName }} {{ props.student.lastName }}
                            </span>
                        </div>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">
                            Student ID: <span class="font-mono">{{ props.student.idNo }}</span>
                        </span>
                    </div>
                    <div class="flex flex-col gap-1">
                        <span class="block text-xs text-gray-500 dark:text-gray-400">
                            Program: <span class="font-mono">{{ props.student.program.name }}</span>
                        </span>
                        <span class="block text-xs text-gray-500 dark:text-gray-400">
                            Entry Year: <span class="font-mono">{{ props.student.year.name }}</span>
                        </span>
                    </div>
                </div>

                <hr class="border-t border-dashed border-gray-400 dark:border-gray-600 my-6" />
                <!-- Payment Details -->
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">Receipt ID:</span>
                        <span class="font-mono text-gray-900 dark:text-gray-100">#{{ props.payment.id }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">Date:</span>
                        <span class="font-mono text-gray-900 dark:text-gray-100">{{ props.payment.payment_date }}</span>
                    </div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">Payment Type:</span>
                        <span class="font-mono text-gray-900 dark:text-gray-100">
                            {{ props.payment.paymentType ? props.payment.paymentType.type : 'N/A' }}
                        </span>
                    </div>
                    <!-- Naration -->
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">Naration:</span>
                        <span class="font-mono text-gray-900 dark:text-gray-100">
                            {{ props.payment.description ? props.payment.description : 'N/A' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">Reference:</span>
                        <span class="font-mono text-gray-900 dark:text-gray-100">
                            {{ props.payment.payment_reference ? props.payment.payment_reference : 'N/A' }}
                        </span>
                    </div>
                    <br>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">Amount:</span>
                        <span class="font-mono text-gray-900 dark:text-gray-100">{{ props.payment.total_amount }} Birr</span>
                    </div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">Status:</span>
                        <span
                            :class="{
                                'text-green-600 dark:text-green-400': props.payment.status === 'completed',
                                'text-yellow-600 dark:text-yellow-400': props.payment.status === 'Pending',
                                'text-indigo-600 dark:text-indigo-400': props.payment.status === 'paid_by_college',
                                'text-red-600 dark:text-red-400': props.payment.status === 'Failed',
                            }"
                            class="font-mono font-semibold"
                        >
                            {{ props.payment.status === 'paid_by_college'
                                ? 'Scholarship'
                                : props.payment.status.charAt(0).toUpperCase() + props.payment.status.slice(1)
                            }}
                        </span>
                    </div>
                    
                    <hr class="border-t border-dashed border-gray-400 dark:border-gray-600 my-4" />

                    <div v-if="props.payment.paymentpaymentItem" class="flex justify-between text-sm mb-1">
                        <span class="text-gray-700 dark:text-gray-300">Linked To:</span>
                        <span class="font-mono text-indigo-600 dark:text-indigo-400">
                            {{ props.payment.paymentpaymentItem.paymentpayment.description }} —
                            {{ props.payment.paymentpaymentItem.name }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-end gap-2 px-6 py-3 bg-white dark:bg-gray-900 print:hidden">
                <button
                    @click="printReceipt"
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded shadow transition"
                >
                    Print Receipt
                </button>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.receipt-paper {
  max-width: 700px;
  font-family: 'ui-monospace', 'SFMono-Regular', 'Menlo', 'Monaco', 'Consolas', 'Liberation Mono', 'Courier New', monospace;
  box-shadow: 0 2px 8px 0 rgba(0,0,0,0.08);
  border-radius: 8px;
  border-width: 2px;
  border-style: dashed;
  background: repeating-linear-gradient(
    to bottom,
    #fff,
    #fff 31px,
    #f3f4f6 32px,
    #fff 33px
  );
  transition: background 0.2s, border-color 0.2s;
}
.dark .receipt-paper {
  background: repeating-linear-gradient(
    to bottom,
    #18181b,
    #18181b 31px,
    #27272a 32px,
    #18181b 33px
  );
  border-color: #52525b;
}
@media print {
  .receipt-paper {
    box-shadow: none !important;
    border-radius: 0 !important;
    margin: 0 !important;
    background: #fff !important;
    color: #000 !important;
  }
  button, .print\:hidden {
    display: none !important;
  }
}
</style>
