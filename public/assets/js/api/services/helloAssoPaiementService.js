import axiosInstance from '../axiosInstance';

/**
 * Service for handling HelloAsso payment-related API calls.
 */
const helloAssoPaiementService = {
  /**
   * Initiates a new payment with HelloAsso.
   */
  initiatePayment: async (paymentData) => {
    const response = await axiosInstance.post('/api/paiements', paymentData);
    return response.data.data;
  },

  /**
   * Retrieves a payment by its ID.
   */
  getPayment: async (id) => {
    const response = await axiosInstance.get(`/api/paiements/${id}`);
    return response.data.data;
  },

  /**
   * Lists payments with optional filters (admin only).
   */
  listPayments: async (filters = {}) => {
    const response = await axiosInstance.get('/api/paiements', { params: filters });
    return response.data.data;
  },

  /**
   * Checks the status of a payment.
   */
  checkPaymentStatus: async (id) => {
    const response = await axiosInstance.get(`/api/paiements/${id}/status`);
    return response.data.data;
  },

  /**
   * Refunds a payment by ID (admin only).
   */
  refundPayment: async (id, refundData) => {
    const response = await axiosInstance.post(`/api/paiements/${id}/refunded`, refundData);
    return response.data.data;
  },
};

export default helloAssoPaiementService;
