import React from 'react';
import { Link } from 'react-router-dom';
import { FaHandshake, FaShieldAlt, FaClock, FaChartLine, FaUsers, FaAward } from 'react-icons/fa';

const AboutPage = () => {
  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <section className="relative bg-gradient-to-r from-primary-900 to-primary-700 text-white py-20 overflow-hidden">
        <div className="absolute inset-0 opacity-10">
          <div className="absolute top-0 left-0 w-96 h-96 bg-white rounded-full filter blur-3xl"></div>
          <div className="absolute bottom-0 right-0 w-96 h-96 bg-primary-300 rounded-full filter blur-3xl"></div>
        </div>
        
        <div className="relative container mx-auto px-4 text-center">
          <h1 className="text-5xl font-bold mb-6" data-aos="fade-down">من نحن</h1>
          <p className="text-xl max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            عقار زين - وجهتك الأولى للعقارات في الإسماعيلية
          </p>
        </div>
      </section>

      {/* القصة */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div data-aos="fade-left">
              <h2 className="text-4xl font-bold mb-6">قصتنا</h2>
              <p className="text-gray-600 text-lg leading-relaxed mb-6">
                تأسست عقار زين في عام 2020 بهدف تغيير مفهوم التسويق العقاري في الإسماعيلية. 
                نحن نؤمن بأن العثور على المنزل المثالي يجب أن يكون تجربة ممتعة وسهلة.
              </p>
              <p className="text-gray-600 text-lg leading-relaxed mb-6">
                على مر السنين، قمنا ببناء سمعة قوية كشركة عقارية موثوقة تقدم خدمات عالية الجودة 
                لعملائنا. نحن نفخر بأننا ساعدنا المئات من العائلات في العثور على منازل أحلامهم.
              </p>
              <div className="flex items-center gap-4">
                <div className="text-center">
                  <div className="text-4xl font-bold text-primary-600">500+</div>
                  <div className="text-gray-600">عقار مباع</div>
                </div>
                <div className="text-center">
                  <div className="text-4xl font-bold text-primary-600">1000+</div>
                  <div className="text-gray-600">عميل سعيد</div>
                </div>
                <div className="text-center">
                  <div className="text-4xl font-bold text-primary-600">50+</div>
                  <div className="text-gray-600">مسوق عقاري</div>
                </div>
              </div>
            </div>
            <div className="grid grid-cols-2 gap-4" data-aos="fade-right">
              <img 
                src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3"
                alt="Office"
                className="rounded-2xl shadow-lg h-64 object-cover"
              />
              <img 
                src="https://images.unsplash.com/photo-1558036117-15d82a90b9b1?ixlib=rb-4.0.3"
                alt="Team"
                className="rounded-2xl shadow-lg h-64 object-cover mt-8"
              />
            </div>
          </div>
        </div>
      </section>

      {/* الرؤية والرسالة */}
      <section className="py-20 bg-white">
        <div className="container mx-auto px-4">
          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div className="bg-gradient-to-br from-primary-50 to-white rounded-3xl p-8 shadow-lg" data-aos="zoom-in">
              <div className="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-6">
                🎯
              </div>
              <h3 className="text-2xl font-bold mb-4">رؤيتنا</h3>
              <p className="text-gray-600 text-lg leading-relaxed">
                أن نكون الشركة العقارية الرائدة في الإسماعيلية، ونقدم تجربة استثنائية 
                في البحث عن العقارات وشرائها وتأجيرها.
              </p>
            </div>

            <div className="bg-gradient-to-br from-primary-50 to-white rounded-3xl p-8 shadow-lg" data-aos="zoom-in" data-aos-delay="100">
              <div className="w-16 h-16 bg-primary-600 text-white rounded-2xl flex items-center justify-center text-2xl mb-6">
                ✨
              </div>
              <h3 className="text-2xl font-bold mb-4">رسالتنا</h3>
              <p className="text-gray-600 text-lg leading-relaxed">
                تسهيل عملية البحث عن العقارات من خلال توفير منصة متكاملة تجمع بين 
                التكنولوجيا الحديثة والخبرة المحلية.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* القيم */}
      <section className="py-20">
        <div className="container mx-auto px-4">
          <div className="text-center mb-12">
            <h2 className="text-4xl font-bold mb-4">قيمنا</h2>
            <p className="text-gray-600 text-lg max-w-2xl mx-auto">
              المبادئ التي توجه عملنا وتشكل ثقافتنا
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {[
              { icon: FaHandshake, title: 'النزاهة', desc: 'نلتزم بأعلى معايير النزاهة والشفافية في جميع تعاملاتنا' },
              { icon: FaShieldAlt, title: 'الموثوقية', desc: 'نبني علاقات طويلة الأمد مبنية على الثقة والموثوقية' },
              { icon: FaClock, title: 'الاحترافية', desc: 'نقدم خدمات احترافية تلبي تطلعات عملائنا' },
              { icon: FaChartLine, title: 'الابتكار', desc: 'نستخدم أحدث التقنيات لتطوير خدماتنا' },
              { icon: FaUsers, title: 'العمل الجماعي', desc: 'نعمل كفريق واحد لتحقيق أفضل النتائج' },
              { icon: FaAward, title: 'الجودة', desc: 'نسعى دائماً لتقديم أفضل جودة في خدماتنا' },
            ].map((value, index) => (
              <div key={index} className="bg-white rounded-2xl shadow-lg p-8 text-center hover:-translate-y-2 transition-all duration-300" data-aos="flip-up" data-aos-delay={index * 100}>
                <div className="w-16 h-16 bg-primary-100 text-primary-600 rounded-2xl flex items-center justify-center text-3xl mx-auto mb-4">
                  <value.icon />
                </div>
                <h3 className="text-xl font-bold mb-3">{value.title}</h3>
                <p className="text-gray-600">{value.desc}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Call to Action */}
      <section className="py-20 bg-primary-900 text-white">
        <div className="container mx-auto px-4 text-center">
          <h2 className="text-4xl font-bold mb-4" data-aos="fade-down">انضم إلى عائلتنا اليوم</h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto" data-aos="fade-up" data-aos-delay="100">
            سواء كنت تبحث عن عقار أو ترغب في بيع عقارك، نحن هنا لمساعدتك
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center" data-aos="zoom-in" data-aos-delay="200">
            <Link to="/contact" className="bg-white text-primary-700 hover:bg-gray-100 font-bold py-4 px-8 rounded-xl transition-all duration-300">
              تواصل معنا
            </Link>
            <Link to="/properties" className="bg-transparent border-2 border-white hover:bg-white hover:text-primary-700 font-bold py-4 px-8 rounded-xl transition-all duration-300">
              تصفح العقارات
            </Link>
          </div>
        </div>
      </section>
    </div>
  );
};

export default AboutPage;
