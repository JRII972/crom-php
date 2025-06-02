import axiosInstance from '../axiosInstance';
import type { PaiementHelloasso } from '../types/db';

/**
 * Service for handling HelloAsso payment-related API calls, typé avec les interfaces.
 */
const helloAssoPaiementService = {
  /**
   * Initiates a new payment with HelloAsso.
   */
  initiatePayment: async (
    paymentData: Partial<PaiementHelloasso> & { id_session?: number; amount?: number; id_utilisateur?: string; return_url?: string; cancel_url?: string }
  ): Promise<{ redirect_url: string; payment_id: string }> => {
    const response = await axiosInstance.post('/api/paiements', paymentData);
    return response.data.data as { redirect_url: string; payment_id: string };
  },

  /**
   * Retrieves a payment by its ID.
   */
  getPayment: async (id: string): Promise<PaiementHelloasso> => {
    const response = await axiosInstance.get(`/api/paiements/${id}`);
    return response.data.data as PaiementHelloasso;
  },

  /**
   * Lists payments with optional filters (admin only).
   */
  listPayments: async (filters = {}): Promise<PaiementHelloasso[]> => {
    const response = await axiosInstance.get('/api/paiements', { params: filters });
    return response.data.data as PaiementHelloasso[];
  },

  /**
   * Checks the status of a payment.
   */
  checkPaymentStatus: async (id: string): Promise<{ status: string }> => {
    const response = await axiosInstance.get(`/api/paiements/${id}/status`);
    return response.data.data as { status: string };
  },

  /**
   * Refunds a payment by ID (admin only).
   */
  refundPayment: async (
    id: string,
    refundData: { amount: number; reason?: string }
  ): Promise<{ refunded: boolean; details?: any }> => {
    const response = await axiosInstance.post(`/api/paiements/${id}/refunded`, refundData);
    return response.data.data as { refunded: boolean; details?: any };
  },
};

export default helloAssoPaiementService;